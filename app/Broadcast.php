<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

namespace App;

use App\Helpers\YoutubeHelper;
use Carbon\Carbon;
use Google_Client;
use Google_Exception;
use Google_Service_YouTube;
use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;

/**
 * Class Broadcast
 * @package App
 */
class Broadcast
{
    /** @var Service */
    protected $service = null;

    /** @var Google_Client */
    protected $client = null;

    /** @var Google_Service_YouTube */
    protected $youtube = null;

    /** @var Google_Service_YouTube_LiveBroadcast */
    protected $liveBroadcast = null;

    /**
     * Broadcast constructor.
     * @param null $service
     * @throws Google_Exception
     */
    public function __construct($service = null)
    {
        $this->setService($service);

        $client = new Google_Client();
        $client->setAuthConfig(base_path('config/client_secret.json'));
        $client->addScope(Google_Service_YouTube::YOUTUBE);
        $this->setClient($client);

        $youtube = new Google_Service_YouTube($client);
        $this->setYoutube($youtube);
    }

    /**
     * @param Service $service
     * @return Broadcast|null
     */
    public static function get(Service $service)
    {
        $instance = new Broadcast($service);
        $instance->authenticate($service->city);
        $broadcast = null;

        $serviceTimeString = self::timeString($service);

        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet',
            ['id' => YoutubeHelper::getCode($service->youtube_url)]
        );

        if (isset($broadcastsResponse['items'][0])) {
            $instance->setLiveBroadcast($broadcastsResponse['items'][0]);
            $broadcast = $broadcastsResponse['items'][0];
        }

        return $broadcast ? $instance : null;
    }

    /**
     * @param City $city
     */
    public function authenticate(City $city)
    {
        $this->client->setAccessToken($city->google_access_token);
        $token = $this->client->refreshToken($city->google_refresh_token);
        $this->client->setAccessToken($token);
    }

    /**
     * @param Service $service
     * @return string
     */
    public static function timeString(Service $service)
    {
        $serviceTime = Carbon::createFromTimeString(
            $service->day->date->format('Y-m-d') . ' ' . $service->time,
            'Europe/Berlin'
        );
        return $serviceTime->setTimezone('UTC')->format('Y-m-d\TH:i:s') . '.000Z';
    }

    /**
     * @return Google_Service_YouTube
     */
    public function getYoutube(): Google_Service_YouTube
    {
        return $this->youtube;
    }

    /**
     * @param Google_Service_YouTube $youtube
     */
    public function setYoutube(Google_Service_YouTube $youtube): void
    {
        $this->youtube = $youtube;
    }

    /**
     * @param Service $service
     * @param string $statusString
     * @return Broadcast
     */
    public static function create(Service $service, $statusString = 'public')
    {
        $instance = new Broadcast($service);

        $instance->authenticate($service->city);

        $liturgy = Liturgy::getDayInfo($service->day);

        $broadcast = null;
        $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
        $broadcastSnippet->setTitle(
            ($service->title ?: (isset($liturgy['title']) ? $liturgy['title'] . ' (' . $service->day->date->format(
                    'd.m.Y'
                ) . ')' : 'Gottesdienst mit ' . $service->participantsText('P', true)))
        );
        $broadcastSnippet->setDescription(
            ($service->title ?: 'Gottesdienst') . ' am ' . $service->day->date->format(
                'd.m.Y'
            ) . (isset($liturgy['title']) ? ' (' . $liturgy['title'] . ')' : '') . ' mit ' . $service->participantsText(
                'P',
                true
            )
        );
        $broadcastSnippet->setScheduledStartTime(self::timeString($service));

        // Create an object for the liveBroadcast resource's status, and set the
        // broadcast's status to "private".
        $status = new Google_Service_YouTube_LiveBroadcastStatus();
        $status->setPrivacyStatus($statusString);
        $status->setSelfDeclaredMadeForKids('false');

        // Create the API request that inserts the liveBroadcast resource.
        $broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
        $broadcastInsert->setSnippet($broadcastSnippet);
        $broadcastInsert->setStatus($status);
        $broadcastInsert->setKind('youtube#liveBroadcast');

        // Execute the request and return an object that contains information
        // about the new broadcast.
        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->insert(
            'snippet,status',
            $broadcastInsert,
            []
        );
        $instance->setLiveBroadcast($broadcastsResponse);

        $video = new \Google_Service_YouTube_Video();
        $video->setId($broadcastsResponse->id);
        $videoSnippet = new \Google_Service_YouTube_VideoSnippet();
        $videoSnippet->setTitle($broadcastSnippet->getTitle());
        $videoSnippet->setDescription($broadcastSnippet->getDescription());
        $videoSnippet->setCategoryId(24);
        $video->setSnippet($videoSnippet);
        $videoStatus = new \Google_Service_YouTube_VideoStatus();
        $videoStatus->setSelfDeclaredMadeForKids(false);
        $video->setStatus($videoStatus);

        $videoResponse = $instance->getYoutube()->videos->update('snippet,status', $video);

        return $instance;
    }

    /**
     * @return string
     */
    public function getSharerUrl()
    {
        return $this->liveBroadcast ? 'https://youtu.be/' . $this->liveBroadcast['id'] : '';
    }

    /**
     * @return string
     */
    public function getLiveDashboardUrl()
    {
        return $this->liveBroadcast ? 'https://studio.youtube.com/video/'
            . $this->liveBroadcast->getId()
            . '/livestreaming' : '';
    }

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }

    /**
     * @param Service $service
     */
    public function setService(Service $service): void
    {
        $this->service = $service;
    }

    /**
     * @return Google_Client
     */
    public function getClient(): Google_Client
    {
        return $this->client;
    }

    /**
     * @param Google_Client $client
     */
    public function setClient(Google_Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @return Google_Service_YouTube_LiveBroadcast
     */
    public function getLiveBroadcast(): Google_Service_YouTube_LiveBroadcast
    {
        return $this->liveBroadcast;
    }

    /**
     * @param Google_Service_YouTube_LiveBroadcast $liveBroadcast
     */
    public function setLiveBroadcast(Google_Service_YouTube_LiveBroadcast $liveBroadcast): void
    {
        $this->liveBroadcast = $liveBroadcast;
    }


}
