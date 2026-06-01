<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Proxy extends Model
{
    protected $fillable = [
        'ip',
        'port',
        'type',
        'status',
        'username',
        'password',
        'last_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'last_checked_at' => 'datetime',
            'port' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }
}
