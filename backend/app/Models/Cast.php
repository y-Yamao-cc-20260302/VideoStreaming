<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Occupation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cast extends Model
{

    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['name', 'gender', 'birthday', 'occupation_id', 'is_publish', 'picture_path'];
    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function scopePublish(Builder $query): Builder
    {
        return $query->where('is_publish', true);
    }

    // videoテーブルに向けて多の接続を作る
    public function video(): BelongsToMany
    {
        // 第三引数が自分のID、第四引数が相手のIDを指定する。
        return $this->belongsToMany(Video::class, 'cast_videos', 'cast_id', 'video_id');
    }

    // 複数のユーザーにお気に入り登録されるのでToMany
    public function user(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'cast_favorites', 'cast_id', 'user_id');
    }

    public function CastFavorites(): HasMany
    {
        return $this->hasMany(CastFavorite::class);
    }
}
