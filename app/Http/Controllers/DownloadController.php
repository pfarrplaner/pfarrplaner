<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    public function download(Request $request, $storage, $code, $prettyName = '') {
        $prettyName = $prettyName ?: '';
        return Storage::download('public/'.$storage.'/'.$code.'.'.pathinfo($prettyName, PATHINFO_EXTENSION), $prettyName);
    }
}
