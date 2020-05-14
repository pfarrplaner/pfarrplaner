<?php


namespace App\Integrations\Youtube;


use App\City;
use App\Integrations\AbstractIntegration;
use Google_Client;
use Google_Service_YouTube;

class YoutubeIntegration extends AbstractIntegration
{

    /** @var Google_Client */
    protected $client = null;

    /** @var Google_Service_YouTube */
    protected $youtube = null;


    public function __construct()
    {
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

    public static function get(City $city) {
        $instance = (new self());
        $instance->authenticate($city);
        return $instance;
    }

    public function getVideo($id) {
        return $this->youtube->videos->listVideos('snippet,contentDetails,statistics', ['id' => $id])->getItems()[0];
    }

    public function getLiveChat($id, $data = []) {
        return $this->getYoutube()->liveChatMessages->listLiveChatMessages($id, 'id,snippet,authorDetails', $data);
    }

    public function sendLiveChatMessage($liveChatId, $author, $message)
    {
        $snippet = new \Google_Service_YouTube_LiveChatMessageSnippet();
        $messageDetails = new \Google_Service_YouTube_LiveChatTextMessageDetails();
        $messageDetails->setMessageText($author.' hat über die Homepage folgende Nachricht eingegeben: '.PHP_EOL.PHP_EOL.$message);
        $snippet->setTextMessageDetails($messageDetails);
        $snippet->setLiveChatId($liveChatId);
        $snippet->setType('textMessageEvent');

        $liveChatInsert = new \Google_Service_YouTube_LiveChatMessage();
        $liveChatInsert->setSnippet($snippet);

        $this->getYoutube()->liveChatMessages->insert('snippet', $liveChatInsert);
    }

    /**
     * Returns true if youtube integration is active for a particular city.
     *
     * This is the case when a youtube channel is specified.
     *
     * @param City $city
     * @return bool
     */
    public static function isActive(City $city): bool
    {
        return ($city->youtube_channel_url != '');
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


}
