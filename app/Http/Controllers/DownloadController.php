<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Helpers\FileHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function download(Request $request, $storage, $code, $prettyName = '') {
        $prettyName = $prettyName ?: '';
        return Storage::download('public/'.$storage.'/'.$code.'.'.pathinfo($prettyName, PATHINFO_EXTENSION), FileHelper::normalizeFilename($prettyName));
    }

    public function attachment(Request $request, Attachment $attachment, $prettyName = '') {
        $prettyName = $prettyName ?  FileHelper::normalizeFilename($prettyName) :
            (FileHelper::normalizeFilename($attachment->title) ?? $attachment->id).'.'.pathinfo($attachment->file, PATHINFO_EXTENSION) ;
        return Storage::download($attachment->file, $prettyName);
    }
}
