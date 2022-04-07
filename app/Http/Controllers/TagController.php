<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

use App\Tag;
use Aws\Inspector\Exception\InspectorException;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * Class TagController
 * @package App\Http\Controllers
 */
class TagController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $tags = Tag::orderBy('name')->get();
        return Inertia::render('Admin/Tag/Index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        $tag = new Tag(['name' => '']);
        return Inertia::render('Admin/Tag/TagEditor', compact('tag'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        Tag::create($this->validateRequest());
        return redirect()->route('tags.index')->with('success', 'Die neue Kennzeichnung wurde angelegt.');
    }

    /**
     * @return mixed
     */
    protected function validateRequest()
    {
        $data = request()->validate(
            [
                'name' => 'string|required',
            ]
        );
        $data['code'] = Str::slug($data['name']);
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Tag $tag
     * @return \Inertia\Response
     */
    public function edit(Tag $tag)
    {
        return Inertia::render('Admin/Tag/TagEditor', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Tag $tag
     * @return Response
     */
    public function update(Tag $tag)
    {
        $tag->update($this->validateRequest());
        return redirect()->route('tags.index')->with('success', 'Die Kennzeichnung wurde geändert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Tag $tag
     * @return Response
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->route('tags.index')->with('success', 'Die Kennzeichnung wurde gelöscht.');
    }
}
