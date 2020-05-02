<?php


namespace App;

use Carbon\Carbon;
use Google_Client;
use Google_Service_YouTube;
use Google_Service_YouTube_LiveBroadcast;
use Google_Service_YouTube_LiveBroadcastSnippet;
use Google_Service_YouTube_LiveBroadcastStatus;

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

    public function authenticate(City $city)
    {
        $this->client->setAccessToken($city->google_access_token);
        $token = $this->client->refreshToken($city->google_refresh_token);
        $this->client->setAccessToken($token);
    }

    public static function timeString(Service $service)
    {
        $serviceTime = Carbon::createFromTimeString(
            $service->day->date->format('Y-m-d') . ' ' . $service->time,
            'Europe/Berlin'
        );
        return $serviceTime->setTimezone('UTC')->format('Y-m-d\TH:i:s') . '.000Z';
    }

    public static function get(Service $service)
    {
        $instance = new Broadcast($service);
        $instance->authenticate($service->city);
        $broadcast = null;

        $serviceTimeString = self::timeString($service);

        $broadcastsResponse = $instance->getYoutube()->liveBroadcasts->listLiveBroadcasts(
            'id,snippet',
            array(
                'mine' => 'true',
            )
        );

        foreach ($broadcastsResponse['items'] as $broadcastItem) {
            if ($serviceTimeString == $broadcastItem['snippet']['scheduledStartTime']) {
                $instance->setLiveBroadcast($broadcastItem);
                $broadcast = $broadcastItem;
            }
        }

        return $broadcast ? $instance : null;
    }

    public static function create(Service $service, $statusString = 'public')
    {
        $instance = new Broadcast($service);

        $instance->authenticate($service->city);

        $liturgy = Liturgy::getDayInfo($service->day);

        $broadcast = null;
        $broadcastSnippet = new Google_Service_YouTube_LiveBroadcastSnippet();
        $broadcastSnippet->setTitle(($service->title ?: ($liturgy['title'] ? $liturgy['title'].' ('.$service->day->date->format('d.m.Y').')' : 'Gottesdienst mit '.$service->participantsText('P', true))));
        $broadcastSnippet->setDescription(($service->title ?: 'Gottesdienst').' am '.$service->day->date->format('d.m.Y') .($liturgy['title'] ? ' ('.$liturgy['title'].')' : '').' mit '.$service->participantsText('P', true));
        $broadcastSnippet->setScheduledStartTime(self::timeString($service));

        // Create an object for the liveBroadcast resource's status, and set the
        // broadcast's status to "private".
        $status = new Google_Service_YouTube_LiveBroadcastStatus();
        $status->setPrivacyStatus($statusString);
        $status->setSelfDeclaredMadeForKids(false);

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
        return $instance;
    }

    public function getSharerUrl()
    {
        return $this->liveBroadcast ? 'https://youtu.be/' . $this->liveBroadcast['id'] : '';
    }

    public function getLiveDashboardUrl()
    {
        return $this->liveBroadcast ? 'https://studio.youtube.com/channel/'
            . $this->liveBroadcast->getSnippet()->getChannelId()
            . '/livestreaming/dashboard?v=' .$this->liveBroadcast->getId() : '';
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
