<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
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

    public function counties(): HasMany
    {
        return $this->hasMany(County::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
