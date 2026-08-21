<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'thumbnail_path',
        'stream_url',
        'duration_sec',
        'release_date',
        'is_published',
    ];

    protected $casts = [
        'release_date' => 'date',
        'is_published' => 'boolean',
        'duration_sec' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class, 'video_genre');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function castvideo(): BelongsToMany
    {
        return $this->belongsToMany(Cast::class, 'cast_videos', 'video_id', 'cast_id');
    }
}
