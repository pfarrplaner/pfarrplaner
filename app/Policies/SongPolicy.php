<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Liturgy\Song;

class SongPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can view the index of all songs.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }


    /**
     * Determine whether the user can view the song.
     *
     * @param User $user
     * @param Song $song
     * @return mixed
     */
    public function view(User $user, Song $song)
    {
        return true;
    }

    /**
     * Determine whether the user can create songs.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the song.
     *
     * @param User $user
     * @param Song $song
     * @return mixed
     */
    public function update(User $user, Song $song)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the song.
     *
     * @param User $user
     * @param Song $song
     * @return mixed
     */
    public function delete(User $user, Song $song)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the song.
     *
     * @param User $user
     * @param Song $song
     * @return mixed
     */
    public function restore(User $user, Song $song)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the song.
     *
     * @param User $user
     * @param Song $song
     * @return mixed
     */
    public function forceDelete(User $user, Song $song)
    {
        return true;
    }

}
