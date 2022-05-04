<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

use App\Liturgy\Songbook;
use App\Services\ResourcePolicyService;
use App\Traits\HandlesAttachedImageTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SongbookController extends Controller
{

    use HandlesAttachedImageTrait;

    protected $model = Songbook::class;

    /**
     * Initialize controller
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Songbook::class, 'songbook');
    }

    /**
     * Show all records
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $songbooks = ResourcePolicyService::attachPermissions(Songbook::orderBy('code')->get());
        return Inertia::render('Admin/Songbook/Index', compact('songbooks'));
    }

    /**
     * Create a new record
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $songbook = new Songbook([
            'name' => '',
            'code' => '',
            'isbn' => '',
            'description' => '',
                                 ]);
        return Inertia::render('Admin/Songbook/SongbookEditor', compact('songbook'));
    }

    /**
     * Store a new record
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Songbook::create($this->validateRequest($request));
        return redirect()->route('songbooks.index')->with('success', 'Das neue Liederbuch wurde angelegt.');
    }

    /**
     * Edit a record
     *
     * @param Songbook $songbook
     * @return \Inertia\Response
     */
    public function edit(Songbook $songbook)
    {
        return Inertia::render('Admin/Songbook/SongbookEditor', compact('songbook'));
    }


    /**
     * Update a record
     *
     * @param Request $request
     * @param Songbook $songbook
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Songbook $songbook)
    {
        $songbook->update($this->validateRequest($request));
        return redirect()->route('songbooks.index')->with('success', 'Die Änderungen wurden gespeichert.');
    }


    /**
     * Delete a record
     *
     * @param Songbook $songbook
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Songbook $songbook)
    {
        $songbook->delete();
        return redirect()->route('songbooks.index')->with('success', 'Das Liederbuch wurde gelöscht.');
    }

    /**
     * Validate the submitted data
     *
     * @param Request $request Request
     * @return array Data
     */
    protected function validateRequest(Request $request) {
        return $request->validate([
                                      'name' => 'required|string',
                                      'code' => 'required|string',
                                      'isbn' => 'nullable|string',
                                      'description' => 'nullable|string',
                                      'image' => 'nullable|string',
                                  ]);
    }
}
