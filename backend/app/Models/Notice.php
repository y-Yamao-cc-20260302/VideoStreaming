<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['title', 'body', 'published_at', 'expired_at'];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('published_at', '<=', now())
            ->where(function (Builder $q) {
                $q->whereNull('expired_at')->orWhere('expired_at', '>', now());
            });
    }
}
