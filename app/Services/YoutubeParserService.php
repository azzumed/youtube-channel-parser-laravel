<?php

namespace App\Services;

use App\Models\Channel;
use Madcoda\Youtube;
use Log;
use Arr;
use Exception;

class YoutubeParserService
{
    protected $api;

    public function __construct(Youtube $api)
    {
        $this->api = $api;
    }

    public function updateActiveChannelPlaylistVideos()
    {
        foreach (Channel::active()->get() as $channel) {
            if ($channel->shouldUpdatePlaylists(now()->addDay())) {
                $this->syncChannelPlaylists($channel);
                $channel->playlists_updated_at = now();
            }
            $this->syncChannelPlaylistVideos($channel);
            $channel->save();
        }
    }

    protected function syncChannelPlaylists(Channel $channel)
    {
        $api = $this->api;

        try {
            $playlists = $api->getPlaylistsByChannelId($channel->external_id);
            if ($playlists) {
                foreach ($playlists as $playlist) {
                    $channel->title = $playlist->snippet->channelTitle;
                    $channel->playlists()->updateOrCreate([
                        'external_id' => $playlist->id,
                    ], [
                        'title' => $playlist->snippet->localized->title,
                        'description' => strlen($playlist->snippet->localized->description) ? $playlist->snippet->localized->description : null,
                    ]);
                }
            }
        } catch (Exception $e) {
            throw_unless(str_contains($e->getMessage(), 'Curl Error'), $e);
            Log::warning(
                'Youtube - get curl error when call getPlaylistsByChannelId(' . $channel->external_id . ') - ' . $e->getMessage()
            );
        }
    }

    protected function syncChannelPlaylistVideos(Channel $channel)
    {
        $api = $this->api;

        foreach ($channel->playlists as $playlist) {
            $pageToken = null;
            while (true) {
                try {
                    $response = $api->getPlaylistItemsByPlaylistIdAdvanced([
                        'playlistId' => $playlist->external_id,
                        'part' => 'snippet, contentDetails',
                        'maxResults' => 50,
                        'pageToken' => $pageToken
                    ], true);

                    if ($response) {
                        //Getting videos duration
                        $videosInfoResponse = $api->getVideosInfo(
                            Arr::pluck($response['results'], 'snippet.resourceId.videoId')
                        );
                        $videosDuration = Arr::pluck($videosInfoResponse, 'contentDetails.duration', 'id');
                        foreach ($videosDuration as &$duration) {
                            $duration = $this->durationToSeconds($duration);
                        }
                        foreach ($response['results'] as $video) {
                            $isPublic = isset($video->contentDetails->videoPublishedAt);
                            $playlist->videos()->firstOrCreate([
                                'external_id' => $video->contentDetails->videoId,
                            ], [
                                'title' => $video->snippet->title,
                                'description' => strlen($video->snippet->description) ? $video->snippet->description : null,
                                'duration' => $videosDuration[$video->contentDetails->videoId] ?? 0,
                                'is_public' => $isPublic,
                                'published_at' => $isPublic ? $video->contentDetails->videoPublishedAt : null,
                            ]);
                        }
                        if (is_null($pageToken = $response['info']['nextPageToken'])) {
                            break;
                        }
                    } else {
                        break;
                    }
                } catch (Exception $e) {
                    throw_unless(str_contains($e->getMessage(), 'Curl Error'), $e);
                    Log::warning(
                        'Youtube - get curl error when call getPlaylistItemsByPlaylistIdAdvanced(' . $channel->external_id . ') - ' . $e->getMessage()
                    );
                }
            }
        }
    }

    public function durationToSeconds($duration)
    {
        //https://stackoverflow.com/a/24393612 and improved for parsing PT5M (5:00) duration
        preg_match_all('/(\d+)/', $duration, $parts);

        // Put in zeros if we have less than 3 numbers.
        if (count($parts[0]) == 1) {
            if (str_ends_with($duration, 'M')) {
                array_push($parts[0], "0");
                array_unshift($parts[0], "0");
            } else {
                array_unshift($parts[0], "0", "0");
            }
        } elseif (count($parts[0]) == 2) {
            array_unshift($parts[0], "0");
        }

        $sec_init = $parts[0][2];
        $seconds = $sec_init % 60;
        $seconds_overflow = floor($sec_init / 60);

        $min_init = $parts[0][1] + $seconds_overflow;
        $minutes = ($min_init) % 60;
        $minutes_overflow = floor(($min_init) / 60);

        $hours = $parts[0][0] + $minutes_overflow;

        return ($hours * 60 * 60) + ($minutes * 60) + $seconds;
    }
}
