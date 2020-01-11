<?php


namespace App\Helpers;


use Illuminate\Support\Facades\Storage;

class FileHelper
{
    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function icon($path) {
        $icons = [
            'application/pdf' => 'fa-file-pdf',
        ];
        return $icons[Storage::mimeType($path)] ?? 'fa-file';
    }

    public static function normalizeFilename ($str = '')
    {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtr($str, ['Ä' => 'Ae', 'ä' => 'ae', 'Ö' => 'Oe', 'ö' => 'oe', 'Ü' => 'Ue', 'ü' => 'ue', 'ß' => 'ss']);

        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '_____', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        $str = str_replace( '_____', ' ', $str);
        return $str;
    }


}
