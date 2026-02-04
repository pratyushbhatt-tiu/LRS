<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected $fillable = [
        'file_no',
        'client_id',
        'doc_type_id',
        'recording_purpose_id',
        'state_id',
        'county_id',
        'partner_ref_no',
        'received_date',
        'current_status',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function docType(): BelongsTo
    {
        return $this->belongsTo(DocType::class);
    }

    public function recordingPurpose(): BelongsTo
    {
        return $this->belongsTo(RecordingPurpose::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(FileStatusHistory::class);
    }
}
