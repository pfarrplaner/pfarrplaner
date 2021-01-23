<?php

namespace App\Http\Controllers;

use App\Liturgy\Resources\BlockResourceCollection;
use App\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiturgyEditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editor(Service $service)
    {
        $service->load('day', 'liturgyBlocks');
        return Inertia::render('liturgyEditor', compact('service'));
    }

    public function save(Request $request, Service $service)
    {
        dd($request->get('blocks'));
        return redirect()->back();
    }
}
