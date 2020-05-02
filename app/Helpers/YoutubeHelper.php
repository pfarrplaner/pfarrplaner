<?php


namespace App\Helpers;


use App\City;

class YoutubeHelper
{

    public static function getCode($url)
    {
        $code = '';
        if (false !== strpos($url, 'youtu.be')) {
            $tmp = explode('/', $url);
            $code = end($tmp);
        }
        return $code;
    }

    public static function getLiveDashboardUrl(City $city, $url) {
        return 'https://studio.youtube.com/channel/'
            .self::getChannelId($city->youtube_channel_url)
            .(substr($city->youtube_channel_url, -1) == '/' ? '' : '/')
            .'livestreaming/dashboard?v='
            .self::getCode($url);
    }

    public static function getChannelId($url) {
        if (substr($url, -1) == '/') $url = substr($url, 0, -1);
        $tmp = explode('/', $url);
        $code = end($tmp);
        return $code;
    }
}
