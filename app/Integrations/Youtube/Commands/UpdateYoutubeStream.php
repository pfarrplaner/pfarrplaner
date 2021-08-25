<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Integrations\Youtube\Commands;

use App\City;
use App\Helpers\YoutubeHelper;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateYoutubeStream extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'youtube:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update streaming keys on youtube';

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
        Log::info('Running youtube:update command');
        $cities = City::where('google_access_token', '!=', '')->get();
        foreach ($cities as $city) {
            $this->output('Checking livestreams for "' . $city->name . '"');

            if ($city->youtube_active_stream_id == '') {
                $this->output('No active stream key defined for "' . $city->name . '".');
                continue;
            }

            if ($city->youtube_passive_stream_id == '') {
                $this->output('No passive stream key defined for "' . $city->name . '".');
                continue;
            }
            $this->output('Keys: '.$city->youtube_active_stream_id.' (active) / '.$city->youtube_passive_stream_id.' (passive)');

            $services = Service::where('city_id', $city->id)
                ->select('services.*')
                ->join('days', 'days.id', 'services.day_id')
                ->where('youtube_url', '!=', '')
                ->orderBy('days.date')
                ->orderBy('time')
                ->get();
            $current = null;
            $youtube = YoutubeIntegration::get($city);
            foreach ($services as $service) {
                $dt = $service->dateTime();
                if ($dt < Carbon::now()) {
                    $last = $service;
                } else {
                    $status = 'future';
                    if (null === $current) {
                        $current = $service;

                        // check status
                        /** @var \Google_Service_YouTube_Video $video */
                        $lastVideo = $youtube->getVideo(YoutubeHelper::getCode($last->youtube_url));
                        if ($this->videoHasEnded($lastVideo)) {
                            $this->output('Last service #' . $last->id . ' (' . $last->formatTime('Y-m-d H:i') . ') has ended.');
                        }

                        $this->output('Next Service #' . $service->id . ' (' . $service->formatTime('Y-m-d H:i') . ')...');
                        $this->setBroadcastOptions($service, true);

                    } else {
                        $this->output('Future Service #' . $service->id . ' (' . $service->formatTime('Y-m-d H:i') . ')...');
                        $this->setBroadcastOptions($service, false);
                    }
                }

            }
        }
    }

    /**
     * Check if video has stopped streaming already
     * @param \Google_Service_YouTube_Video $video
     * @return bool
     */
    private function videoHasEnded(\Google_Service_YouTube_Video $video) {
        return (null !== $video->getLiveStreamingDetails()->actualEndTime);
    }

    /**
     * Configure the broadcast for a service with specific options
     * @param Service $service Service
     * @param bool $active Set active or inactive
     */
    private function setBroadcastOptions(Service $service, $active = true) {
        $autoStartStop = $active;
        $youtube = YoutubeIntegration::get($service->city);
        $broadcasts = $youtube->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,contentDetails',
            ['id' => YoutubeHelper::getCode($service->youtube_url)]);

        if (count($broadcasts->getItems())) {
            /** @var \Google_Service_YouTube_LiveBroadcast $broadcast */
            $broadcast = $broadcasts->getItems()[0];
            $contentDetails = $broadcast->getContentDetails();

            if (($contentDetails->getBoundStreamId() == ($active ? $service->city->youtube_passive_stream_id : $service->city_youtube_active_stream_id))
                || (null === $contentDetails->getBoundStreamId())) {
                $this->output('Setting '.($active ? 'active' : 'passive').' stream key for service #'.$service->id);
                $youtube->getYoutube()->liveBroadcasts->bind($broadcast->getId(), 'id,snippet,status', ['streamId' => ($active ? $service->city->youtube_active_stream_id : $service->city_youtube_passive_stream_id)]);
            } else {
                $this->output('Stream is not set to '.($active ? 'passive' : 'active').' key, but to '.$contentDetails->getBoundStreamId().' --> leaving it alone.');
            }
            if ($service->city->youtube_auto_startstop) {
                if (($contentDetails->getEnableAutoStart() != $autoStartStop) || ($contentDetails->getEnableAutoStop() != $autoStartStop)) {
                    $this->output('Enabling auto start/stop for service #'.$service->id);
                    $contentDetails->setEnableAutoStart($autoStartStop ? 'true' : 'false');
                    $contentDetails->setEnableAutoStop($autoStartStop ? 'true' : 'false');
                    $broadcast->setContentDetails($contentDetails);
                    $youtube->getYoutube()->liveBroadcasts->update('contentDetails', $broadcast);
                }
            }
        }

    }

}
