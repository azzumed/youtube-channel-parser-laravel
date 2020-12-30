<?php

namespace App\Console\Commands;

use App\Services\YoutubeParserService;
use Illuminate\Console\Command;

class ParseChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Parses playlists and it's videos from channels";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param YoutubeParserService $service
     * @return void
     */
    public function handle(YoutubeParserService $service)
    {
        $service->updateActiveChannelPlaylistVideos();

        $this->info('Success!');
    }
}
