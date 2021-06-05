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

/**
 * Trait HasCommentsTrait
 * @package App\Traits
 */
trait HasCommentsTrait
{

    /**
     * @return mixed
     */
    public function publicComments()
    {
        return $this->comments()->where('private', 0);
    }

    /**
     * @return mixed
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * @return mixed
     */
    public function commentsForCurrentUser()
    {
        return $this->commentsForUser(Auth::user());
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function commentsForUser(User $user)
    {
        return $this->comments()->where(
            function ($query) use ($user) {
                $query->where('private', 0);
                $query->orWhere('user_id', $user->id);
            }
        );
    }


}
