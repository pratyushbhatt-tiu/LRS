<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\County;
use App\Models\DocType;
use App\Models\File;
use App\Models\FileStatusHistory;
use App\Models\ImportLog;
use App\Models\RecordingPurpose;
use App\Models\State;
use App\Services\AuditService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Background queue job that processes a CSV bulk import of files.
 *
 * For each valid data row it:
 *  1. Resolves FK IDs from human-readable codes/names
 *  2. Creates a File record with auto-generated file_no / partner_ref_no
 *  3. Creates the initial FileStatusHistory entry
 *
 * Invalid rows are collected into the ImportLog error column for download.
 */
class ImportFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Maximum attempts before the job is marked as failed.
     */
    public int $tries = 1;

    public function __construct(
        private readonly int $importLogId,
        private readonly int $userId,
    ) {
    }

    public function handle(): void
    {
        $importLog = ImportLog::findOrFail($this->importLogId);
        $importLog->update(['status' => 'processing']);

        $errors = [];
        $successRows = 0;
        $totalRows = 0;

        try {
            // Read CSV from local storage
            $csvPath = Storage::disk('local')->path($importLog->file_path);
            $handle = fopen($csvPath, 'r');

            if (!$handle) {
                throw new \RuntimeException("Cannot open CSV file: {$importLog->file_path}");
            }

            // Parse the header row (case-insensitive)
            $headers = array_map('strtolower', array_map('trim', fgetcsv($handle)));

            // Map expected column positions
            $colMap = array_flip($headers);

            while (($row = fgetcsv($handle)) !== false) {
                $totalRows++;
                $rawData = array_combine($headers, $row);

                // --- Row-level validation ---
                $rowError = $this->validateRow($rawData, $totalRows);
                if ($rowError) {
                    $errors[] = array_merge(
                        ['row' => $totalRows],
                        $rawData,
                        ['error' => $rowError]
                    );
                    continue;
                }

                // Convert received_date from DD-MM-YYYY (CSV format) to Y-m-d (DB format)
                [$day, $month, $year] = explode('-', trim($rawData['received_date']));
                $parsedDate = "{$year}-{$month}-{$day}";

                // --- Resolve foreign keys from codes/names ---
                $client = Client::where('code', trim($rawData['client_code']))->where('active', true)->first();
                $docType = DocType::where('code', trim($rawData['doc_type_code']))->where('active', true)->first();
                $recordingPurpose = RecordingPurpose::where('code', trim($rawData['recording_purpose_code']))->where('active', true)->first();
                $state = State::where('code', trim($rawData['state_code']))->where('active', true)->first();
                $county = $state ? County::where('name', trim($rawData['county_name']))->where('state_id', $state->id)->where('active', true)->first() : null;

                // Collect any FK resolution failures
                $fkErrors = [];
                if (!$client)
                    $fkErrors[] = "client_code '{$rawData['client_code']}' not found";
                if (!$docType)
                    $fkErrors[] = "doc_type_code '{$rawData['doc_type_code']}' not found";
                if (!$recordingPurpose)
                    $fkErrors[] = "recording_purpose_code '{$rawData['recording_purpose_code']}' not found";
                if (!$state)
                    $fkErrors[] = "state_code '{$rawData['state_code']}' not found";
                if (!$county)
                    $fkErrors[] = "county_name '{$rawData['county_name']}' not found in state";

                if (!empty($fkErrors)) {
                    $errors[] = array_merge(
                        ['row' => $totalRows],
                        $rawData,
                        ['error' => implode('; ', $fkErrors)]
                    );
                    continue;
                }

                // --- Idempotency Check ---
                // Prevent duplicate files from being imported twice by checking exact match of attributes
                $isDuplicate = File::where('client_id', $client->id)
                    ->where('doc_type_id', $docType->id)
                    ->where('recording_purpose_id', $recordingPurpose->id)
                    ->where('state_id', $state->id)
                    ->where('county_id', $county->id)
                    ->where('received_date', $parsedDate)
                    ->exists();

                if ($isDuplicate) {
                    $errors[] = array_merge(
                        ['row' => $totalRows],
                        $rawData,
                        ['error' => 'Idempotency exception: A duplicate file with these exact details already exists']
                    );
                    continue;
                }

                // --- Create the File record inside a transaction ---
                try {
                    DB::transaction(function () use ($rawData, $parsedDate, $client, $docType, $recordingPurpose, $state, $county) {
                        $year = now()->year;
                        $lrsPrefix = "LRS-{$year}-";
                        $refPrefix = "REF-{$year}-";

                        // Generate next LRS sequence number (pessimistic lock)
                        $lastFile = File::where('file_no', 'like', $lrsPrefix . '%')
                            ->orderBy('file_no', 'desc')
                            ->lockForUpdate()
                            ->first();

                        $newSeq = $lastFile
                            ? str_pad((int) substr($lastFile->file_no, strlen($lrsPrefix)) + 1, 6, '0', STR_PAD_LEFT)
                            : '000001';
                        $fileNo = $lrsPrefix . $newSeq;

                        // Generate partner reference number
                        $lastRef = File::where('partner_ref_no', 'like', $refPrefix . '%')
                            ->orderBy('partner_ref_no', 'desc')
                            ->lockForUpdate()
                            ->first();

                        $newRefSeq = $lastRef
                            ? str_pad((int) substr($lastRef->partner_ref_no, strlen($refPrefix)) + 1, 6, '0', STR_PAD_LEFT)
                            : '000001';
                        $partnerRefNo = $refPrefix . $newRefSeq;

                        $file = File::create([
                            'file_no' => $fileNo,
                            'partner_ref_no' => $partnerRefNo,
                            'client_id' => $client->id,
                            'doc_type_id' => $docType->id,
                            'recording_purpose_id' => $recordingPurpose->id,
                            'state_id' => $state->id,
                            'county_id' => $county->id,
                            'received_date' => $parsedDate,
                            'current_status' => config('constants.file_statuses.CHECK_IN'),
                        ]);

                        FileStatusHistory::create([
                            'file_id' => $file->id,
                            'from_status' => null,
                            'to_status' => $file->current_status,
                            'changed_by' => $this->userId,
                            'notes' => 'Imported via CSV bulk import',
                        ]);
                    });

                    $successRows++;
                } catch (\Throwable $e) {
                    $errors[] = array_merge(
                        ['row' => $totalRows],
                        $rawData,
                        ['error' => 'Database error: ' . $e->getMessage()]
                    );
                }
            }

            fclose($handle);

            // --- Finalize the ImportLog ---
            $importLog->update([
                'status' => 'done',
                'total_rows' => $totalRows,
                'success_rows' => $successRows,
                'failed_rows' => count($errors),
                'errors' => !empty($errors) ? json_encode($errors) : null,
            ]);

            // Audit log the bulk import event
            AuditService::log('BULK_IMPORT', $importLog, [], [
                'total' => $totalRows,
                'success' => $successRows,
                'failed' => count($errors),
            ]);

        } catch (\Throwable $e) {
            // Mark the log as failed if the job itself crashes
            $importLog->update(['status' => 'failed']);
            throw $e;
        }
    }

    /**
     * Validate a single CSV row. Returns an error string or null if valid.
     */
    private function validateRow(array $row, int $rowNumber): ?string
    {
        $required = ['client_code', 'received_date', 'doc_type_code', 'recording_purpose_code', 'state_code', 'county_name'];
        $missing = [];

        foreach ($required as $field) {
            if (empty(trim($row[$field] ?? ''))) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return 'Missing required fields: ' . implode(', ', $missing);
        }

        // Validate date format: must be DD-MM-YYYY
        $dateTrimmed = trim($row['received_date']);
        if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $dateTrimmed)) {
            return "Invalid received_date format (expected DD-MM-YYYY): '{$row['received_date']}'";
        }
        [$d, $m, $y] = explode('-', $dateTrimmed);
        if (!checkdate((int) $m, (int) $d, (int) $y)) {
            return "Invalid received_date value (not a real date): '{$row['received_date']}'";
        }

        return null;
    }
}
