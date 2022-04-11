<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Http\Controllers;


use App\Liturgy\Item;
use App\Liturgy\Music\ABCMusic;
use App\Liturgy\Psalm;
use App\Liturgy\Song;
use App\Liturgy\SongVerse;
use App\Services\ResourcePolicyService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SongController extends Controller
{

    /**
     * SongController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Song::class, 'song');
    }

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $songs = ResourcePolicyService::attachPermissions(Song::all());
        return Inertia::render('Admin/Song/Index', compact('songs'));
    }

    /**
     * Create a new record
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $song = new Song([
                             'title' => '',
                             'refrain' => '',
                             'copyrights' => '',
                             'key' => '',
                             'measure' => '',
                             'note_length' => '',
                             'prolog' => '',
                             'notation' => '',
                             'refrain_notation' => '',
                             'refrain_text_notation' => '',
                         ]);
        $song->songbooks = [];
        $song->verses = [];
        return Inertia::render('Admin/Song/SongEditor', compact('song'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function songbooks()
    {
        $songbooks = [];
        foreach (Song::all() as $song) {
            $songbooks[$song->songbook_abbreviation] = [
                'title' => $song->songbook,
                'abbreviation' => $song->songbook_abbreviation
            ];
        }
        foreach (Psalm::all() as $song) {
            $songbooks[$song->songbook_abbreviation] = [
                'title' => $song->songbook,
                'abbreviation' => $song->songbook_abbreviation
            ];
        }
        return response()->json($songbooks);
    }

    /**
     * Edit a record
     *
     * @param Song $song
     * @return \Inertia\Response
     */
    public function edit(Song $song)
    {
        return Inertia::render('Admin/Song/SongEditor', compact('song'));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $song = Song::create($data);
        foreach ($data['verses'] as $verse) {
            $verse['song_id'] = $song->id;
            SongVerse::create($verse);
        }
        $song->syncSongbooksFromRequest($data);
        return redirect()->route('songs.index')->with('success', 'Das neue Lied wurde gespeichert.');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request, Song $song)
    {
        $data = $this->validateRequest($request);
        $song->update($data);
        $song->verses()->delete();
        foreach ($data['verses'] as $verse) {
            $verse['song_id'] = $song->id;
            SongVerse::create($verse);
        }

        $song->syncSongbooksFromRequest($data);
        return redirect()->route('songs.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * @param Song $song
     * @return RedirectResponse
     */
    public function destroy(Song $song)
    {
        $song->delete();
        return redirect()->route('songs.index')->with('success', 'Das Lied wurde gelöscht.');
    }

    /**
     * Create a separate song from a songbook reference on an existing one
     *
     * @param Song $song
     * @param $reference
     * @return RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function split(Song $song, $reference)
    {
        $this->authorize('update', $song);



        $song->load('songbooks');

        $origSync = $splitSync = [];
        foreach ($song->songbooks as $songbook) {
            if ($songbook->pivot->songbook_id == $reference) {
                $splitSync[$songbook->pivot->songbook_id] = ['reference' => $songbook->pivot->reference, 'code' => $songbook->pivot->code];
            } else {
                $origSync[$songbook->pivot->songbook_id] = ['reference' => $songbook->pivot->reference, 'code' => $songbook->pivot->code];
            }
        }

        $song->songbooks()->sync([]);
        $song->songbooks()->sync($origSync);

        $newSong = $song->replicate();
        $newSong->save();
        $newSong->refresh();
        $newSong->songbooks()->sync([]);
        $newSong->songbooks()->sync($splitSync);
        $newSong->push();
        $newSong->refresh();

        return redirect()->route('song.edit', $newSong->id);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate(
            [
                'title' => 'required|string',
                'refrain' => 'nullable|string',
                'copyrights' => 'nullable|string',
                'key' => 'nullable|string',
                'measure' => 'nullable|string',
                'note_length' => 'nullable|string',
                'notation' => 'nullable|string',
                'refrain_notation' => 'nullable|string',
                'refrain_text_notation' => 'nullable|string',
                'verses.*.number' => 'nullable',
                'verses.*.text' => 'nullable|string',
                'verses.*.refrain_before' => 'nullable|bool',
                'verses.*.refrain_after' => 'nullable|bool',
                'verses.*.notation' => 'nullable|string',
                'songbooks.*.code' => 'nullable|string',
                'songbooks.*.pivot.songbook_id' => 'nullable|int|exists:songbooks,id',
                'songbooks.*.pivot.reference' => 'nullable|string',
            ]
        );
    }

    public function musicEditor(Song $song)
    {
        return Inertia::render('Liturgy/Songs/MusicEditor', compact('song'));
    }

    public function music(Song $song, $verses = '', $lineNumber = null)
    {
        return response()->file(ABCMusic::renderToFile($song, $verses, ABCMusic::make($song, $verses, $lineNumber)));
    }

}
