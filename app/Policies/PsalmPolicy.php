<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Liturgy\Psalm;

class PsalmPolicy
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
     * Determine whether the user can view the index of all psalms.
     *
     * @param User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }


    /**
     * Determine whether the user can view the psalm.
     *
     * @param User $user
     * @param Psalm $psalm
     * @return mixed
     */
    public function view(User $user, Psalm $psalm)
    {
        return true;
    }

    /**
     * Determine whether the user can create psalms.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the psalm.
     *
     * @param User $user
     * @param Psalm $psalm
     * @return mixed
     */
    public function update(User $user, Psalm $psalm)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the psalm.
     *
     * @param User $user
     * @param Psalm $psalm
     * @return mixed
     */
    public function delete(User $user, Psalm $psalm)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the psalm.
     *
     * @param User $user
     * @param Psalm $psalm
     * @return mixed
     */
    public function restore(User $user, Psalm $psalm)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the psalm.
     *
     * @param User $user
     * @param Psalm $psalm
     * @return mixed
     */
    public function forceDelete(User $user, Psalm $psalm)
    {
        return true;
    }

}
