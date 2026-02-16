<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    // Relationships
    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function feeRules()
    {
        return $this->hasMany(FeeRule::class);
    }
}
