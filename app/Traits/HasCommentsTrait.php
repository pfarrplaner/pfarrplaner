<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 13.05.2019
 * Time: 18:50
 */

namespace App\Traits;


use App\Comment;
use App\User;
use Illuminate\Support\Facades\Auth;

trait HasCommentsTrait
{

    public function comments() {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function publicComments() {
        return $this->comments()->where('private', 0);
    }

    public function commentsForUser(User $user) {
        return $this->comments()->where(function($query) use ($user) {
            $query->where('private', 0);
            $query->orWhere('user_id', $user->id);
        });
    }

    public function commentsForCurrentUser() {
        return $this->commentsForUser(Auth::user());
    }


}