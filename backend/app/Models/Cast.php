<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cast extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name', 'gender', 'birthday', 'occupation_id', 'is_publish', 'picture_path'];
    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }
}
