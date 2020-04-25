<?php


namespace App\Helpers;


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
}
