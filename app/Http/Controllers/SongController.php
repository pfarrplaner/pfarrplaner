<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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


use App\Liturgy\Song;
use App\Liturgy\SongVerse;
use Illuminate\Http\Request;

class SongController extends Controller
{

    /**
     * SongController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json(Song::all());
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
        $songs = Song::all();
        return response()->json(compact('song', 'songs'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
        $song->refresh();
        $song->load('verses');
        $songs = Song::all();
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
                'title' => 'required|string',
                'refrain' => 'nullable|string',
                'copyrights' => 'nullable|string',
                'songbook' => 'nullable|string',
                'songbook_abbreviation' => 'nullable|string',
                'reference' => 'nullable|string',
                'verses.*.number' => 'nullable',
                'verses.*.text' => 'nullable|string',
                'verses.*.refrain_before' => 'nullable|bool',
                'verses.*.refrain_after' => 'nullable|bool',
            ]
        );
    }

}
