<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

use App\Comment;
use App\Service;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CommentController
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if ($request->has('body')) {
            $commentableType = $request->get('commentable_type');
            $commentableId = $request->get('commentable_id');
            $owner = app($commentableType)->find($commentableId);
            if ($this->canCommentOnThisObject($owner)) {
                $comment = $owner->comments()->create(
                    [
                        'body' => $request->get('body'),
                        'private' => $request->get('private'),
                        'user_id' => Auth::user()->id
                    ]
                );
                if ($request->header('Accept', '') == 'application/json') {
                    $comment->load('user');
                    return response()->json($comment);
                }
                return view('partials.comments.single', compact('comment'));
            } else {
                if ($request->header('Accept', '') == 'application/json') {
                    return response()->json(['message' => 'Leider hast du keine Berechtigung zum Kommentieren dieses Objekts.'], 401);
                }
                return '<div class="alert alert-danger">Leider hast du keine Berechtigung zum Kommentieren dieses Objekts.</div>';
            }
        }
        return '';
    }

    /**
     * Check if current user may comment on a specific object
     * @param $owner Object to be commented on
     * @return bool true if commenting is allowed
     */
    protected function canCommentOnThisObject($owner)
    {
        $owningService = is_a($owner, Service::class) ? $owner : $owner->service;
        if (null === $owningService) {
            return Auth::user()->writableCities->pluck('id')->contains($owner->city_id);
        }
        return Auth::user()->can('update', $owningService);
    }

    /**
     * Display the specified resource.
     *
     * @param Comment $comment
     * @return Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment)
    {
        return view('partials.comments.form', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    public function update(Request $request, Comment $comment)
    {
        $comment->body = $request->get('body') ?: '';
        $comment->private = $request->get('private') ?: '';
        $comment->save();
        return view('partials.comments.single', compact('comment'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return '<div class="alert alert-success alert-dismissible alertCommentDeleted"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Der Kommentar wurde gelöscht.</div>';
    }
}
