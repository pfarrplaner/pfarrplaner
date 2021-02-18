<?php

namespace App\Http\Controllers;

use App\Sermon;
use App\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SermonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editorByService(Service $service)
    {
        $sermon = $service->sermon;
        if ($sermon) {
            $services = $sermon->services;
        } else {
            $services = collect([$service]);
        }
        return Inertia::render('sermonEditor', compact('services', 'sermon', 'service'));
    }

    public function editor(Sermon $sermon)
    {
        $services = $sermon->services;
        return Inertia::render('sermonEditor', compact('services', 'sermon'));
    }

    public function store(Request $request, Service $service)
    {
        $data = $this->validateRequest($request);
        $sermon = Sermon::create($data);
        if (null !== $sermon) {
            $service->update(['sermon_id' => $sermon->id]);
            return redirect()->route('sermon.editor', $sermon->id);
        }
        return redirect()->back();
    }

    public function update(Request $request, Sermon $sermon)
    {
        $data = $this->validateRequest($request);
        $sermon->update($data);
        return redirect()->back();
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate(
            [
                'title' => 'nullable|string',
                'subtitle' => 'nullable|string',
                'reference' => 'nullable|string',
                'series' => 'nullable|string',
                'summary' => 'nullable|string',
                'text' => 'nullable|string',
                'notes_header' => 'nullable|string',
                'key_points' => 'nullable|string',
                'questions' => 'nullable|string',
                'literature' => 'nullable|string',
                'audio_recording' => 'nullable|string',
                'video_url' => 'nullable|string',
                'external_url' => 'nullable|string',
                'cc_license' => 'nullable|boolean',
                'permit_handouts' => 'nullable|boolean',
            ]
        );
    }
}
