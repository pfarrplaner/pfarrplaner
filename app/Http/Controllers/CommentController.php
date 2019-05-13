<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->has('body')) {
            $commentableType = $request->get('commentable_type');
            $commentableId = $request->get('commentable_id');
            $owner = $commentableType::find($commentableId);
            if (Auth::user()->can('update', $owner)) {
                $comment = new Comment([
                    'body' => $request->get('body'),
                    'private' => $request->get('private'),
                    'commentable_id' => $commentableId,
                    'commentable_type' => $commentableType,
                    'user_id' => Auth::user()->id,
                ]);
                $comment->save();
                return view('partials.comments.single', compact('comment'));
            } else {
                return '<div class="alert alert-danger">Leider hast du keine Berechtigung zum Kommentieren dieses Objekts.</div>';
            }
        }
        return '';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        return view('partials.comments.form', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
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
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return '<div class="alert alert-success alert-dismissible alertCommentDeleted"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>Der Kommentar wurde gelöscht.</div>';
    }
}
