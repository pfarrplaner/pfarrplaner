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

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Liturgy\Music\ABCMusic;
use App\Liturgy\Psalm;
use App\Liturgy\Song;
use App\Liturgy\SongReference;
use App\Liturgy\SongVerse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SongController extends Controller
{

    /**
     * SongController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(SongReference::orderBy('code')->orderBy('reference')->get());
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request)['song'];
        $song = Song::create($data);
        foreach ($data['verses'] as $verse) {
            $verse['song_id'] = $song->id;
            SongVerse::create($verse);
        }
        $song->syncSongbooksFromRequest($data);
        $song->refresh();
        $song->load('verses');
        $songs = SongReference::orderBy('code')->orderBy('reference')->get();
        return response()->json(compact('song', 'songs'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Song $song)
    {
        $data = $this->validateRequest($request)['song'];
        $song->update($data);
        $song->verses()->delete();
        foreach ($data['verses'] as $verse) {
            $verse['song_id'] = $song->id;
            SongVerse::create($verse);
        }

        $song->syncSongbooksFromRequest($data);
        $song->refresh();
        $song->load('verses');
        $songs = SongReference::orderBy('code')->orderBy('reference')->get();
        return response()->json(compact('song', 'songs'));
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate(
            [
                'song.title' => 'required|string',
                'song.refrain' => 'nullable|string',
                'song.copyrights' => 'nullable|string',
                'song.key' => 'nullable|string',
                'song.measure' => 'nullable|string',
                'song.note_length' => 'nullable|string',
                'song.notation' => 'nullable|string',
                'song.refrain_notation' => 'nullable|string',
                'song.refrain_text_notation' => 'nullable|string',
                'song.verses.*.number' => 'nullable',
                'song.verses.*.text' => 'nullable|string',
                'song.verses.*.refrain_before' => 'nullable|bool',
                'song.verses.*.refrain_after' => 'nullable|bool',
                'song.verses.*.notation' => 'nullable|string',
                'song.songbooks.*.code' => 'nullable|string',
                'song.songbooks.*.pivot.songbook_id' => 'nullable|int|exists:songbooks,id',
                'song.songbooks.*.pivot.reference' => 'nullable|string',
            ]
        );
    }

    public function music(Song $song, $verses = '', $lineNumber = null)
    {
        return response()->file(ABCMusic::renderToFile($song, $verses, ABCMusic::make($song, $verses, $lineNumber)));
    }



}
