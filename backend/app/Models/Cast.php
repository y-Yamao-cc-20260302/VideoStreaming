<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Occupation;

class Cast extends Model
{
    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }
}
