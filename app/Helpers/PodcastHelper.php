<?php


namespace App\Helpers;


class PodcastHelper
{

    public static function getFileSize($url)
    {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_exec($curl);
	return curl_getinfo($curl, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
    }
}
