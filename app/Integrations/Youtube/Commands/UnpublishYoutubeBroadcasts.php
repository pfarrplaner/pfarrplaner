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

use App\Broadcast;
use App\City;
use App\Helpers\YoutubeHelper;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
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

            $this->output('Configured cut-off after '.$city->youtube_cutoff_days.' days');
            $cutoffDate = Carbon::now()->subDays($city->youtube_cutoff_days);
            $this->output('Cut-off date: '.$cutoffDate->format('Y-m-d H:i:s'));

            $services = Service::inCity($city)
                ->where('youtube_url', '!=', '')
                ->startingFrom($cutoffDate->copy()->subDays(1))
                ->endingAt($cutoffDate)
                ->orderedDesc()
                ->limit(10)
                ->get();

            foreach ($services as $service) {
                $this->output('Checking broadcast for service #'.$service->id);
                if ($broadcastRecord = Broadcast::get($service)) {
                    $broadcast = $broadcastRecord->getLiveBroadcast();
                    $pubDate = (new Carbon($broadcast->getSnippet()->getScheduledStartTime()))
                        ->shiftTimezone('UTC')->setTimezone('Europe/Berlin');
                    if ($broadcast->getStatus()->getPrivacyStatus() == 'public') {
                        $this->output('Unpublishing broadcast id ' . $broadcast->getId()
                                      . ' (' . $pubDate->format('Y-m-d H:i:s').')'
                        );

                        $status = $broadcast->getStatus();
                        $status->setPrivacyStatus('private');
                        $broadcast->setStatus($status);

                        $youtube->getYoutube()->liveBroadcasts->update('snippet,status,contentDetails', $broadcast);
                    } else {
                        $this->output('Broadcast id ' . $broadcast->getId()
                                      . ' (' . $pubDate->format('Y-m-d H:i:s').') is already unpublished.'
                        );
                    }
                } else {
                    $this->output('No corresponding broadcast found.');
                }
            }

        }
    }
}
