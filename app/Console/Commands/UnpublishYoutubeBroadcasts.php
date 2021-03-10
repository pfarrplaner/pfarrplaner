<?php

namespace App\Console\Commands;

use App\City;
use App\Helpers\YoutubeHelper;
use App\Integrations\Youtube\YoutubeIntegration;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UnpublishYoutubeBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:unpublish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Unpublish old youtube videos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function output($s) {
        $this->line($s);
        Log::info($s);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cities = City::where('youtube_channel_url', '!=', '')->where('youtube_cutoff_days', '>', 0)->get();
        foreach ($cities as $city) {

            $this->output('Checking videos for "' . $city->name . '"');
            $youtube = YoutubeIntegration::get($city);
            $channelId = YoutubeHelper::getChannelId($city->youtube_channel_url);
            $this->output('Checking channel ' . $channelId);
            /** @var \Google_Service_YouTube_Channel $channel */
            $channel = $youtube->getYoutube()->channels->listChannels(
                'id,snippet,contentDetails',
                ['id' => $channelId]
            )->getItems()[0];

            $this->output('Configured cut-off after '.$city->youtube_cutoff_days.' days');
            $cutoffDate = Carbon::now()->subDays($city->youtube_cutoff_days);
            $this->output('Cut-off date: '.$cutoffDate->format('Y-m-d H:i:s'));

            $allBroadcasts = [];
            $broadcasts = $youtube->getYoutube()->liveBroadcasts->listLiveBroadcasts(
                'id,snippet,status',
                [
                    'mine' => 'true',
                    'maxResults' => 50
                ]
            );
            foreach ($broadcasts->getItems() as $broadcast) {
                $allBroadcasts[] = $broadcast;
            }
            while (null !== ($token = $broadcasts->getNextPageToken())) {
                $broadcasts = $youtube->getYoutube()->liveBroadcasts->listLiveBroadcasts(
                    'id,snippet,status',
                    [
                        'mine' => 'true',
                        'maxResults' => 50,
                        'pageToken' => $token
                    ]
                );
                foreach ($broadcasts->getItems() as $broadcast) {
                    $allBroadcasts[] = $broadcast;
                }
            }

            /** @var \Google_Service_YouTube_LiveBroadcast $broadcast */
            foreach ($allBroadcasts as $broadcast) {
                $pubDate = new Carbon($broadcast->getSnippet()->getScheduledStartTime());
                if ($pubDate < $cutoffDate) {
                    if ($broadcast->getStatus()->getPrivacyStatus() == 'public') {
                        $this->output('Unpublishing broadcast id ' . $broadcast->getId()
                                    . ' (' . $pubDate->format('Y-m-d H:i:s').')'
                        );

                        $status = $broadcast->getStatus();
                        $status->setPrivacyStatus('private');
                        $broadcast->setStatus($status);

                        $youtube->getYoutube()->liveBroadcasts->update('snippet,status', $broadcast);
                    }
                } else {
                    $this->output('Leaving broadcast id ' . $broadcast->getId()
                                . ' (' . $pubDate->format('Y-m-d H:i:s').') online.'
                    );
                }
            }
        }
    }
}
