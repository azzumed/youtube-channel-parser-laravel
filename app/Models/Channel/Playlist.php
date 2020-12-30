<?php

namespace App\Models\Channel;

use App\Models\Channel;
use App\Models\Channel\Playlist\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Channel\Playlist
 *
 * @property int $id
 * @property int $channel_id
 * @property string $title
 * @property string|null $description
 * @property string $external_id
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Channel $channel
 * @property-read \Illuminate\Database\Eloquent\Collection|Video[] $videos
 * @property-read int|null $videos_count
 * @method static Builder|Playlist active()
 * @method static Builder|Playlist newModelQuery()
 * @method static Builder|Playlist newQuery()
 * @method static Builder|Playlist query()
 * @method static Builder|Playlist whereActive($value)
 * @method static Builder|Playlist whereChannelId($value)
 * @method static Builder|Playlist whereCreatedAt($value)
 * @method static Builder|Playlist whereDescription($value)
 * @method static Builder|Playlist whereExternalId($value)
 * @method static Builder|Playlist whereId($value)
 * @method static Builder|Playlist whereTitle($value)
 * @method static Builder|Playlist whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Playlist extends Model
{
    protected $fillable = ['title', 'description', 'external_id', 'active'];

    protected $table = 'channel_playlists';

    protected $casts = [
        'active' => 'bool',
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function scopeActive(Builder $q)
    {
        $q->where('active', true);
    }
}
