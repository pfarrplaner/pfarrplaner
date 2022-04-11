<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Liturgy\Songbook;

class SongbookPolicy
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
     * Determine whether the user can view the index of all songbooks.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }


    /**
     * Determine whether the user can view the songbook.
     *
     * @param User $user
     * @param Songbook $songbook
     * @return mixed
     */
    public function view(User $user, Songbook $songbook)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

    /**
     * Determine whether the user can create services.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

    /**
     * Determine whether the user can update the songbook.
     *
     * @param User $user
     * @param Songbook $songbook
     * @return mixed
     */
    public function update(User $user, Songbook $songbook)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

    /**
     * Determine whether the user can delete the songbook.
     *
     * @param User $user
     * @param Songbook $songbook
     * @return mixed
     */
    public function delete(User $user, Songbook $songbook)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

    /**
     * Determine whether the user can restore the songbook.
     *
     * @param User $user
     * @param Songbook $songbook
     * @return mixed
     */
    public function restore(User $user, Songbook $songbook)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

    /**
     * Determine whether the user can permanently delete the songbook.
     *
     * @param User $user
     * @param Songbook $songbook
     * @return mixed
     */
    public function forceDelete(User $user, Songbook $songbook)
    {
        return $user->isAdmin || $user->isLocalAdmin || $user->hasPermissionTo('liederbuecher-bearbeiten');
    }

}
