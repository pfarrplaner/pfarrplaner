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


use App\Liturgy\Text;
use Illuminate\Http\Request;

class LiturgicalTextsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
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
            ]
        );
    }

}
