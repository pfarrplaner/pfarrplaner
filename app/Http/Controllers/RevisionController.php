<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Venturecraft\Revisionable\Revision;

class RevisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

    }

    public function index(Request $request) {
        $key = $request->get('key', '');
        $old = $request->get('old', '');
        $new = $request->get('new', '');
        if ($request->has('key')) {
            $revisions = Revision::where('key', $key);
            if ($old) $revisions->where('old_value', $old);
            if ($new) $revisions->where('old_value', $new);
            $revisions = $revisions->get();
        } else {
            $revisions = new Collection();
        }

        $keys = Revision::distinct()->get(['key'])->pluck('key');
        return view('revisions.index', compact('revisions', 'key', 'old', 'new', 'keys'));
    }

    public function revert(Request $request) {
        $request->validate(['key' => 'required']);
        $key = $request->get('key');
        $revisions = Revision::whereIn('id', $request->get('revisions') ?: [])->get();
        /** @var Revision $revision */
        foreach ($revisions as $revision) {
            $service = $revision->revisionable;
            $service->setAttribute($key, $revision->old_value);
            $service->save();
        }
        return redirect()->route('revisions.index', ['key' => $key])->with('success', count($revisions).' Änderungen wurden rückgängig gemacht.');
    }
}
