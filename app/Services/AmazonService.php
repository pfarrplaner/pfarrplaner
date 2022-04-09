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

namespace App\Services;

class AmazonService
{

    public static function getInfoForISBN($isbn)
    {
        $ItemId = $_GET['isbn'];
        $ResponseGroup = 'Offers';
        $Timestamp = gmdate("Y-m-d\TH:i:s\Z");
        $AWSAccessKeyId = '/'.config('amazon.id').'/';
        $Version = "2013-08-01";
        $str = "Service=AWSECommerceService&Operation=ItemLookup&ResponseGroup=" . $ResponseGroup . "&IdType=ASIN&ItemId=" . urlencode(
                $ItemId
            ) . "&AWSAccessKeyId=" . $AWSAccessKeyId . "&Timestamp=" . urlencode(
                $Timestamp
            );

        $ar = explode("&", $str);
        natsort($ar);
        $str = "GET webservices.amazon.com /onca/xml";
        $str .= implode("&", $ar);
        $str = urlencode(base64_encode(hash_hmac("sha256", $str, '/your secret/', true)));
        $url = "http://webservices.amazon.com/onca/xml?Service=AWSECommerceService&Operation=ItemLookup&ResponseGroup=Offers&IdType=ASIN&ItemId=" . $ItemId . "&AssociateTag=your-tag&AWSAccessKeyId=" . $AWSAccessKeyId . "&Timestamp=" . urlencode(
                $Timestamp
            ) . "&Signature=" . $str;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        dump($result);
        $xml = simplexml_load_string($result) or die("Error: Cannot create object");
        dd($xml);
    }

}
