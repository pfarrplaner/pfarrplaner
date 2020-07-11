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
