<?php

namespace App\Http\Controllers;

use App\Sermon;
use App\Service;
use App\Traits\HandlesAttachmentsTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SermonController extends Controller
{

    use HandlesAttachmentsTrait;

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
        $this->handleIndividualAttachment($request, $sermon, 'image');
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
        $this->handleIndividualAttachment($request, $sermon, 'image');
        return redirect()->route('sermon.editor', $sermon);
    }

    public function uncouple(Request $request, Service $service)
    {
        if (null === $service->sermon) abort(404);
        /** @var Sermon $sermon */
        $sermon = $service->sermon;
        $service->update(['sermon_id' => null]);
        $sermon->refresh();
        if (count($sermon->services) == 0) {
            $sermon->delete();
            return redirect()->route('services.liturgy.editor', $service->id);
        } else {
            return redirect()->route('sermon.editor', $sermon->id);
        }
    }

    protected function handleCheckBoxes($data, $keys) {
        foreach ($keys as $key) {
            if (null !== $data[$key]) $data[$key] = (bool)$data[$key];
        }
        return $data;
    }

    protected function validateRequest(Request $request)
    {
        $data = $request->validate(
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
                'cc_license' => 'nullable',
                'permit_handouts' => 'nullable',
            ]
        );
        $data = $this->handleCheckBoxes($data, ['cc_license', 'permit_handouts']);
        return $data;
    }
}
