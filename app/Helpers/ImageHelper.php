<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Helpers;

class ImageHelper
{

    public const IPTC_OBJECT_NAME = '2#005';
    public const IPTC_EDIT_STATUS = '2#007';
    public const IPTC_PRIORITY = '2#010';
    public const IPTC_CATEGORY = '2#015';
    public const IPTC_SUPPLEMENTAL_CATEGORY = '2#020';
    public const IPTC_FIXTURE_IDENTIFIER = '2#022';
    public const IPTC_KEYWORDS = '2#025';
    public const IPTC_RELEASE_DATE = '2#030';
    public const IPTC_RELEASE_TIME = '2#035';
    public const IPTC_SPECIAL_INSTRUCTIONS = '2#040';
    public const IPTC_REFERENCE_SERVICE = '2#045';
    public const IPTC_REFERENCE_DATE = '2#047';
    public const IPTC_REFERENCE_NUMBER = '2#050';
    public const IPTC_CREATED_DATE = '2#055';
    public const IPTC_CREATED_TIME = '2#060';
    public const IPTC_ORIGINATING_PROGRAM = '2#065';
    public const IPTC_PROGRAM_VERSION = '2#070';
    public const IPTC_OBJECT_CYCLE = '2#075';
    public const IPTC_BYLINE = '2#080';
    public const IPTC_BYLINE_TITLE = '2#085';
    public const IPTC_CITY = '2#090';
    public const IPTC_PROVINCE_STATE = '2#095';
    public const IPTC_COUNTRY_CODE = '2#100';
    public const IPTC_COUNTRY = '2#101';
    public const IPTC_ORIGINAL_TRANSMISSION_REFERENCE = '2#103';
    public const IPTC_HEADLINE = '2#105';
    public const IPTC_CREDIT = '2#110';
    public const IPTC_SOURCE = '2#115';
    public const IPTC_COPYRIGHT_STRING = '2#116';
    public const IPTC_CAPTION = '2#120';
    public const IPTC_LOCAL_CAPTION = '2#121';


    /**
     * Get the width of an image
     * @param $image Path to image
     * @return int Width
     */
    public static function getWidth($image) {
        if (!file_exists($image)) return 0;
        $dimensions = getimagesize($image);
        return $dimensions[0] ?? 0;
    }

    /**
     * Get the height of an image
     * @param $image Path to image
     * @return int Height
     */
    public static function getHeight($image) {
        if (!file_exists($image)) return 0;
        $dimensions = getimagesize($image);
        return $dimensions[1] ?? 0;
    }

    /**
     * Get the proportional width of an image calculated from new height
     * @param $image Path to image
     * @param $newHeight New height
     * @return int New width
     */
    public static function getProportionalWidth($image, $newHeight) {
        if (!file_exists($image)) return 0;
        $dimensions = getimagesize($image);
        if (!isset($dimensions[0])) return 0;

        /** @var array $dimensions */
        return floor(($dimensions[0] / $dimensions[1]) * $newHeight);
    }

    /**
     * Get the proportional height of an image calculated from new width
     * @param $image Path to image
     * @param $newWidth New width
     * @return int New height
     */
    public static function getProportionalHeight($image, $newWidth) {
        if (!file_exists($image)) return 0;
        $dimensions = getimagesize($image);
        if (!isset($dimensions[1])) return 0;

        /** @var array $dimensions */
        return floor(($dimensions[1] / $dimensions[0]) * $newWidth);
    }

    /**
     * Get the IPTC data embedded in an image
     * @param $image Path to image
     * @return array IPTC data
     */
    public static function IPTC($image)
    {
        if (!file_exists($image)) return [];
        $dimensions = getimagesize($image, $meta);
        if (!isset($meta["APP13"])) return [];
        return iptcparse($meta["APP13"]);
    }


    /**
     * Get an embedded license string from IPTC data
     * @param $image Path to image
     * @param false $withUrl True, if a license URL should be included
     * @return string License string
     */
    public static function getEmbeddedLicenseString($image, $withUrl = false) {
        if (!file_exists($image)) '';
        $iptc = self::IPTC($image);
        if (!count($iptc)) return '';
        if (!isset($iptc[self::IPTC_COPYRIGHT_STRING])) return '';
        $copyright = is_array($iptc[self::IPTC_COPYRIGHT_STRING]) ? $iptc[self::IPTC_COPYRIGHT_STRING][0] : $iptc[self::IPTC_COPYRIGHT_STRING];

        if (!$withUrl) {
            if (false !== ($x = strpos($copyright, 'http'))) {
                $copyright = trim(substr($copyright, 0, $x-1), ' ,');
            }
        }
        return $copyright;
    }

}
