<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App;

use App\Helpers\YoutubeHelper;
use App\Integrations\Youtube\YoutubeIntegration;
use Carbon\Carbon;
use Google\Exception;
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

    /** @var City */
    protected $city = null;

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
        if (null !== $service) $this->setService($service);

        $client = new Google_Client();
        $client->setAuthConfig(base_path('config/client_secret.json'));
        $client->addScope(Google_Service_YouTube::YOUTUBE);
        $this->setClient($client);

        $youtube = new Google_Service_YouTube($client);
        $this->setYoutube($youtube);
    }

    public static function getFromId($id, $city) {
        $instance = new Broadcast();
        $instance->authenticate($city);
        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,contentDetails,status',
            ['id' => $id]
        );
        if (isset($broadcastsResponse['items'][0])) {
            $instance->setLiveBroadcast($broadcastsResponse['items'][0]);
            $broadcast = $broadcastsResponse['items'][0];
            $service = Service::where('youtube_url', 'https://youtu.be/'.$id)->first();
            if (null !== $service) $instance->setService($service);
        }
        return $broadcast ? $instance : null;
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

        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,contentDetails,status',
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
        if ($this->getCity() != $city) $this->setCity($city);
        $this->client->setAccessToken($city->google_access_token);
        $token = $this->client->refreshToken($city->google_refresh_token);
        $this->client->setAccessToken($token);
    }

    /**
     * @param Service $service
     * @return string
     */

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
        $broadcastSnippet = $service->getBroadcastSnippet();

        // Create an object for the liveBroadcast resource's status, and set the
        // broadcast's status to "private".
        $status = new Google_Service_YouTube_LiveBroadcastStatus();
        $status->setPrivacyStatus($statusString);
        $status->setSelfDeclaredMadeForKids('false');

        // Create the API request that inserts the liveBroadcast resource.
        $broadcastInsert = new Google_Service_YouTube_LiveBroadcast();
        $broadcastInsert->setSnippet($service->getBroadcastSnippet());
        $broadcastInsert->setStatus($status);
        $broadcastInsert->setKind('youtube#liveBroadcast');

        // Execute the request and return an object that contains information
        // about the new broadcast.
        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->insert(
            'snippet,status,contentDetails',
            $broadcastInsert,
            []
        );
        $instance->setLiveBroadcast($broadcastsResponse);

        $video = new \Google_Service_YouTube_Video();
        $video->setId($broadcastsResponse->id);
        $video->setSnippet($service->getVideoSnippet());
        $videoStatus = new \Google_Service_YouTube_VideoStatus();
        $videoStatus->setSelfDeclaredMadeForKids($service->city->youtube_self_declared_for_children == 1);
        $video->setStatus($videoStatus);

        $videoResponse = $instance->getYoutube()->videos->update('snippet,status', $video);

        return $instance;
    }

    public static function delete(Service $service)
    {
        $youtube = YoutubeIntegration::get($service->city)->getYoutube();
        $youtube->liveBroadcasts->delete(YoutubeHelper::getCode($service->youtube_url));
        $service->update(['youtube_url' => '']);
    }

    public function update() {
        $broadcast = $this->getLiveBroadcast();
        $broadcast->setSnippet($this->service->getBroadcastSnippet());
        $this->getYoutube()->liveBroadcasts->update('id,snippet,contentDetails,status', $broadcast);
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


    /**
     * Get the currently active broadcast for a city
     * @param City $city
     */
    public static function getActive(City $city)
    {
        if (!$city->youtube_active_stream_id) {
            return [];
        }
        $instance = new Broadcast();
        $instance->authenticate($city);
        $broadcasts = [];

        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,status,contentDetails,contentDetails',
            ['mine' => true]
        );

        foreach ($broadcastsResponse->getItems() as $broadcastItem) {
            if ($broadcastItem->getContentDetails()->boundStreamId == $city->youtube_active_stream_id
                && $broadcastItem->getStatus()->getLifeCycleStatus() != 'complete') {
                $broadcastItem->service = Service::where('youtube_url', 'https://youtu.be/'.$broadcastItem->getId())->first();
                $broadcasts[] = $broadcastItem;

            }
        }
        return $broadcasts;
    }

    public static function getInactive(City $city)
    {
        if (!$city->youtube_active_stream_id) {
            return [];
        }
        $instance = new Broadcast();
        $instance->authenticate($city);
        $broadcasts = [];

        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,status,contentDetails',
            ['mine' => true]
        );

        foreach ($broadcastsResponse->getItems() as $broadcastItem) {
            if ($broadcastItem->getContentDetails()->boundStreamId != $city->youtube_active_stream_id && $broadcastItem->getStatus()->getLifeCycleStatus() != 'complete') {
                $broadcastItem->service = Service::where('youtube_url', 'https://youtu.be/'.$broadcastItem->getId())->first();
                $broadcasts[] = $broadcastItem;

            }
        }
        return $broadcasts;

    }

    public function activate()
    {
        $this->authenticate($this->getCity());

        $broadcast = $this->getLiveBroadcast();

        $this->getYoutube()->liveBroadcasts->bind($broadcast->getId(), 'id,snippet,status', ['streamId' => $this->getCity()->youtube_active_stream_id]);
        $broadcastsResponse = $this->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,contentDetails',
            ['id' => $broadcast->getId()]
        );
        $broadcast = $broadcastsResponse->getItems()[0];

        if ($this->getCity()->youtube_auto_startstop) {
            $contentDetails = $broadcast->getContentDetails();
            if ((!$contentDetails->getEnableAutoStart()) || (!$contentDetails->getEnableAutoStop())) {
                $contentDetails->setEnableAutoStart('true');
                $contentDetails->setEnableAutoStop('true');
                $broadcast->setContentDetails($contentDetails);
                $this->getYoutube()->liveBroadcasts->update('snippet,contentDetails', $broadcast);
            }
        }

        // set all others to passive:
        $broadcastsResponse = $this->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet,status,contentDetails',
            ['mine' => true]
        );
        foreach ($broadcastsResponse->getItems() as $item) {
            if (($item->getId() != $broadcast->getId())
                && ($item->getStatus()->getLifeCycleStatus() != 'complete')
                && ($item->getContentDetails()->getBoundStreamId() == $this->getCity()->youtube_active_stream_id)) {
                try {
                    $this->getYoutube()->liveBroadcasts->bind($item->getId(), 'id,snippet,status', ['streamId' => $this->getCity()->youtube_passive_stream_id]);
                } catch (\Google\Service\Exception $e) {
                    dd ($item, $e);
                }
            }
        }


    }

    /**
     * @return City
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City $city
     */
    public function setCity(?City $city): void
    {
        $this->city = $city;
    }



}
