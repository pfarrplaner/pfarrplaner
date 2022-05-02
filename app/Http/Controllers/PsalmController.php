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


use App\Liturgy\Psalm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PsalmController extends Controller
{

    /**
     * PsalmController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $psalms = Psalm::all();
        return Inertia::render('Admin/Psalm/Index', compact('psalms'));
    }

    /**
     * @return \Inertia\Response
     */
    public function create()
    {
        $psalm = new Psalm();
        return Inertia::render('Admin/Psalm/PsalmEditor', compact('psalm'));
    }

    /**
     * @param Psalm $psalm
     * @return \Inertia\Response
     */
    public function edit(Psalm $psalm)
    {
        return Inertia::render('Admin/Psalm/PsalmEditor', compact('psalm'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);
        $psalm = Psalm::create($data);
        $psalms = Psalm::all();
        return redirect()->route('psalms.index')->with('success', 'Der Psalm wurde gespeichert.');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request, Psalm $psalm)
    {
        $data = $this->validateRequest($request);

        $psalm->update($data);
        $psalm->refresh();
        $psalms = Psalm::all();
        return redirect()->route('psalms.index')->with('success', 'Der Psalm wurde gespeichert.');
    }

    /**
     * @param Psalm $psalm
     * @return RedirectResponse
     */
    public function destroy(Psalm $psalm)
    {
        $psalm->delete();
        return redirect()->route('psalms.index')->with('success', 'Der Psalm wurde gelöscht.');
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
                'intro' => 'nullable|string',
                'text' => 'nullable|string',
                'copyrights' => 'nullable|string',
                'songbook' => 'nullable|string',
                'songbook_abbreviation' => 'nullable|string',
                'reference' => 'nullable|string',
            ]
        );
    }

}
