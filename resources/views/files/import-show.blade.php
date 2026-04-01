<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center text-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('files.import') }}"
                   class="p-2 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 leading-tight">Import Summary</h2>
                    <p class="text-gray-500 text-xs mt-0.5">{{ $importLog->filename }}</p>
                </div>
            </div>

            {{-- Error CSV download (only if there are failed rows) --}}
            @if($importLog->failed_rows > 0 && $importLog->isComplete())
                <a href="{{ route('files.import.errors', $importLog) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-red-700 rounded-2xl hover:bg-red-100 transition shadow-sm text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download Error CSV
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Status Banner --}}
            @php
                $bannerClass = match($importLog->status) {
                    'pending', 'processing' => 'bg-blue-50 border-blue-200 text-blue-800',
                    'done' => ($importLog->failed_rows > 0 ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-green-50 border-green-200 text-green-800'),
                    'failed' => 'bg-red-50 border-red-200 text-red-800',
                    default => 'bg-gray-50 border-gray-200 text-gray-700',
                };
            @endphp

            <div class="border rounded-2xl px-6 py-4 {{ $bannerClass }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($importLog->isProcessing())
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span class="font-semibold text-sm">Import is being processed... Please refresh the page in a few moments.</span>
                        @elseif($importLog->status === 'done' && $importLog->failed_rows === 0)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="font-semibold text-sm">Import completed successfully — all rows imported!</span>
                        @elseif($importLog->status === 'done' && $importLog->failed_rows > 0)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-semibold text-sm">Import completed with errors — some rows were skipped. Download the error CSV to review.</span>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span class="font-semibold text-sm">Import job failed. Please try again or contact support.</span>
                        @endif
                    </div>
                    <span class="text-xs font-bold uppercase tracking-wide opacity-70">{{ $importLog->status_label }}</span>
                </div>
            </div>

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $stats = [
                        ['label' => 'Total Rows', 'value' => $importLog->total_rows, 'color' => 'gray'],
                        ['label' => 'Imported', 'value' => $importLog->success_rows, 'color' => 'green'],
                        ['label' => 'Failed', 'value' => $importLog->failed_rows, 'color' => 'red'],
                        ['label' => 'Success Rate', 'value' => $importLog->total_rows > 0
                            ? round(($importLog->success_rows / $importLog->total_rows) * 100) . '%'
                            : 'N/A', 'color' => $importLog->failed_rows === 0 ? 'green' : 'amber'],
                    ];
                @endphp

                @foreach($stats as $stat)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                        <div class="text-2xl font-black text-{{ $stat['color'] }}-600">{{ $stat['value'] }}</div>
                        <div class="text-xs text-gray-500 font-medium mt-1 uppercase tracking-wide">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- Error Table (if any) --}}
            @if($importLog->isComplete() && $importLog->failed_rows > 0)
                @php $errorRows = $importLog->getErrorsArray(); @endphp
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 bg-red-50/50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-900">Failed Rows ({{ count($errorRows) }})</h3>
                        <a href="{{ route('files.import.errors', $importLog) }}"
                           class="text-xs text-red-600 hover:text-red-800 font-semibold">Download Error CSV →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500 font-bold">
                                <tr>
                                    <th class="px-4 py-3 text-left">Row #</th>
                                    <th class="px-4 py-3 text-left">Client Code</th>
                                    <th class="px-4 py-3 text-left">Doc Type</th>
                                    <th class="px-4 py-3 text-left">State</th>
                                    <th class="px-4 py-3 text-left">County</th>
                                    <th class="px-4 py-3 text-left">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($errorRows as $errorRow)
                                    <tr class="bg-red-50/30 hover:bg-red-50/60">
                                        <td class="px-4 py-3 font-mono font-bold text-gray-700">{{ $errorRow['row'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $errorRow['client_code'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $errorRow['doc_type_code'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $errorRow['state_code'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $errorRow['county_name'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-red-700 font-medium">{{ $errorRow['error'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Info Row --}}
            <div class="text-xs text-gray-400 text-center">
                Uploaded {{ $importLog->created_at->format('d-m-Y \a\t H:i') }}
                by {{ $importLog->user->name }}
            </div>

        </div>
    </div>
</x-app-layout>
