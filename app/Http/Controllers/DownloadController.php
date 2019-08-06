<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{


    protected function normalizeString ($str = '')
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

    public function download(Request $request, $storage, $code, $prettyName = '') {
        $prettyName = $prettyName ?: '';
        return Storage::download('public/'.$storage.'/'.$code.'.'.pathinfo($prettyName, PATHINFO_EXTENSION), $this->normalizeString($prettyName));
    }
}
