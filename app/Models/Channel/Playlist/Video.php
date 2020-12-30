<?php

namespace App\Models\Channel\Playlist;

use App\Models\Channel\Playlist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Channel\Playlist\Video
 *
 * @property int $id
 * @property int $playlist_id
 * @property string $title
 * @property string|null $description
 * @property string $external_id
 * @property int $duration
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $url
 * @property-read Playlist $playlist
 * @method static Builder|Video active()
 * @method static Builder|Video newModelQuery()
 * @method static Builder|Video newQuery()
 * @method static Builder|Video query()
 * @method static Builder|Video whereCreatedAt($value)
 * @method static Builder|Video whereDescription($value)
 * @method static Builder|Video whereDuration($value)
 * @method static Builder|Video whereExternalId($value)
 * @method static Builder|Video whereId($value)
 * @method static Builder|Video whereIsPublic($value)
 * @method static Builder|Video wherePlaylistId($value)
 * @method static Builder|Video wherePublishedAt($value)
 * @method static Builder|Video whereTitle($value)
 * @method static Builder|Video whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'external_id',
        'duration',
        'is_public',
        'published_at'
    ];

    protected $table = 'channel_playlist_videos';

    protected $casts = [
        'is_public' => 'bool',
        'published_at' => 'date',
    ];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }

    public function getUrlAttribute()
    {
        return 'https://www.youtube.com/watch?v=' . $this->external_id;
    }

    public function scopeActive(Builder $q)
    {
        $q->where('active', true);
    }
}
