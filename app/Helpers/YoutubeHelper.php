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

namespace App\Helpers;


use App\City;

/**
 * Class YoutubeHelper
 * @package App\Helpers
 */
class YoutubeHelper
{

    /**
     * @param City $city
     * @param $url
     * @return string
     */
    public static function getLiveDashboardUrl(City $city, $url)
    {
        return 'https://studio.youtube.com/video/'
            . self::getCode($url).'/livestreaming';
    }

    /**
     * @param $url
     * @return mixed|string
     */
    public static function getChannelId($url)
    {
        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }
        $tmp = explode('/', $url);
        $code = end($tmp);
        return $code;
    }

    /**
     * @param $url
     * @return mixed|string
     */
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
