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

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Venturecraft\Revisionable\Revision;

/**
 * Class RevisionController
 * @package App\Http\Controllers
 */
class RevisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        $key = $request->get('key', '');
        $old = $request->get('old', '');
        $new = $request->get('new', '');
        if ($request->has('key')) {
            $revisions = Revision::where('key', $key);
            if ($old) {
                $revisions->where('old_value', $old);
            }
            if ($new) {
                $revisions->where('old_value', $new);
            }
            $revisions = $revisions->get();
        } else {
            $revisions = new Collection();
        }

        $keys = Revision::distinct()->get(['key'])->pluck('key');
        return view('revisions.index', compact('revisions', 'key', 'old', 'new', 'keys'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function revert(Request $request)
    {
        $request->validate(['key' => 'required']);
        $key = $request->get('key');
        $revisions = Revision::whereIn('id', $request->get('revisions') ?: [])->get();
        /** @var Revision $revision */
        foreach ($revisions as $revision) {
            $service = $revision->revisionable;
            $service->setAttribute($key, $revision->old_value);
            $service->save();
        }
        return redirect()->route('revisions.index', ['key' => $key])->with(
            'success',
            count(
                $revisions
            ) . ' Änderungen wurden rückgängig gemacht.'
        );
    }
}
