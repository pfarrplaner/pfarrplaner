<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\Helpers\FileHelper;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['storage','file','image']);
    }

    public function download(Request $request, $storage, $code, $prettyName = '')
    {
        $prettyName = $prettyName ?: '';
        return Storage::download(
            'public/' . $storage . '/' . $code . '.' . pathinfo($prettyName, PATHINFO_EXTENSION),
            FileHelper::normalizeFilename($prettyName)
        );
    }

    public function attachment(Request $request, Attachment $attachment, $prettyName = '')
    {
        if (get_class($attachment->attachable) == Service::class) {
            if (!Auth::user()->can('update', $attachment->attachable)) {
                abort(403);
            }
            if (!Auth::user()->can('gd-bearbeiten')) {
                abort(403);
            }
            if (!Auth::user()->writableCities->contains($attachment->attachable->city)) {
                abort(403);
            }
        } else {
            if (!Auth::user()->can('update', $attachment->attachable->service)) {
                abort(403);
            }
            if (!Auth::user()->can('gd-kasualien-bearbeiten')) {
                abort(403);
            }
            if (!Auth::user()->writableCities->contains($attachment->attachable->service->city)) {
                abort(403);
            }
        }

        $prettyName = $prettyName ? FileHelper::normalizeFilename($prettyName) :
            (FileHelper::normalizeFilename($attachment->title) ?? $attachment->id) . '.' . pathinfo(
                $attachment->file,
                PATHINFO_EXTENSION
            );
        return Storage::download($attachment->file, $prettyName);
    }

    public function storage($path, $prettyName = '')
    {
        if ((pathinfo($path, PATHINFO_EXTENSION)  == '') && (pathinfo($prettyName, PATHINFO_EXTENSION) != '')) {
            $path .= '.' . pathinfo($prettyName, PATHINFO_EXTENSION);
        }
        $prettyName = $prettyName ? FileHelper::normalizeFilename($prettyName) : basename($path);
        if (Storage::exists('attachments/' . $path)) {
            return Storage::download('attachments/' . $path, $prettyName);
        }
        abort(404);
    }


    public function image($path, $prettyName = '')
    {
        if ((pathinfo($path, PATHINFO_EXTENSION)  == '') && (pathinfo($prettyName, PATHINFO_EXTENSION) != '')) {
            $path .= '.' . pathinfo($prettyName, PATHINFO_EXTENSION);
        }
        $prettyName = $prettyName ? FileHelper::normalizeFilename($prettyName) : basename($path);
        if (Storage::exists('attachments/' . $path)) {
            return Storage::download('attachments/' . $path, $prettyName);

	    $file = File::get('attachments/'.$path);
	    $type = File::mimeType('attachments/'.$path);
	
	    $response = Response::make($file, 200);
	    $response->header("Content-Type", $type);

	    return $response;

        }
        abort(404);
    }




}
