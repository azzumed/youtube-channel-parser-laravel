<?php

namespace App\Models;

use App\Models\Channel\Playlist;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Channel
 *
 * @property int $id
 * @property string|null $title
 * @property string $external_id
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $playlists_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Playlist[] $playlists
 * @property-read int|null $playlists_count
 * @method static Builder|Channel active()
 * @method static Builder|Channel newModelQuery()
 * @method static Builder|Channel newQuery()
 * @method static Builder|Channel query()
 * @method static Builder|Channel whereActive($value)
 * @method static Builder|Channel whereCreatedAt($value)
 * @method static Builder|Channel whereExternalId($value)
 * @method static Builder|Channel whereId($value)
 * @method static Builder|Channel wherePlaylistsUpdatedAt($value)
 * @method static Builder|Channel whereTitle($value)
 * @method static Builder|Channel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Channel extends Model
{
    protected $fillable = ['title', 'external_id', 'active'];

    protected $casts = [
        'active' => 'bool',
        'playlists_updated_at' => 'date',
    ];

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function shouldUpdatePlaylists(Carbon $expiresAt = null)
    {
        return $this->playlists_updated_at === null
            || (
                $expiresAt !== null && $this->playlists_updated_at->clone()->add($expiresAt->diff(now()))->isPast()
            );
    }

    public function scopeActive(Builder $q)
    {
        $q->where('active', true);
    }
}
