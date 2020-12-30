<?php

namespace App\Http\Controllers;

use App\Services\YoutubeParserService;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function __invoke()
    {
        return app(YoutubeParserService::class)->updateActiveChannelPlaylistVideos();
    }
}
