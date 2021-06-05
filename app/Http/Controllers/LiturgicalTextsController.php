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


use App\Liturgy\Text;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LiturgicalTextsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $texts = Text::all();
        return Inertia::render('texts', compact('texts'));
    }

    public function list()
    {
        return response()->json(Text::all());
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $text = Text::create($data);
        $texts = Text::all();
        return response()->json(compact('text', 'texts'));
    }

    /**
     * @param Request $request
     * @param Text $text
     */
    public function update(Request $request, Text $text)
    {
        $data = $this->validateRequest($request);
        $text->update($data);
        $texts = Text::all();
        return response()->json(compact('text', 'texts'));
    }

    public function import(Request $request)
    {
        $data = $request->validate(['data' => 'required|string'])['data'];
        $records = [];
        $record = $newRecord = [
            'agenda_code' => '',
            'title' => '',
            'text' => [],
            'notice' => [],
            'source' => [],
            'needs_replacement' => '',
        ];
        $code = '';
        foreach (explode("\n", $data) as $line) {
            if (substr($line, 0, 4) == '    ') {
                $line = "\t".trim($line);
            } else {
                $line = trim($line);
            }
            if ($line == '---') {
                // insert line breaks
                foreach (['text', 'notice', 'source'] as $key) {
                    $record[$key] = join("\n", $record[$key]);
                }
                // check if replacement is needed
                foreach (['funeral' => 'bestattung', 'baptism' => 'taufe', 'wedding' => 'trauung'] as $keyCode => $key) {
                    if (false !== strpos($record['text'], '['.$key)) $record['needs_replacement'] = $keyCode;
                }
                if (trim($record['text'])) {
                    $records[] = Text::create($record);
                }
                $record = $newRecord;
                $record['agenda_code'] = $code;
            } elseif(substr($line, 0, 1) == '#') {
                $record['agenda_code'] = $code = trim(substr($line, 1));
            } elseif(substr($line, 0, 2) == 'T:') {
                $record['title'] = trim(substr($line, 2));
            } elseif(substr($line, 0, 2) == 'Q:') {
                $record['source'][] = trim(substr($line, 2));
            } elseif(substr($line, 0, 2) == '//') {
                $record['notice'][] = trim(substr($line, 2));
            } else {
                $record['text'][] = $line;
            }
        }
        return redirect()->route('liturgy.text.index');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function validateRequest(Request $request)
    {
        return $request->validate(
            [
                'title' => 'required|string',
                'text' => 'required|string',
                'agenda_code' => 'nullable|string',
                'needs_replacement' => 'nullable|string',
                'source' => 'nullable|string',
                'notice' => 'nullable|string',
            ]
        );
    }

}
