<?php

namespace App\Policies;

use App\User;
use App\Day;
use Illuminate\Auth\Access\HandlesAuthorization;

class DayPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the day.
     *
     * @param  \App\User  $user
     * @param  \App\Day  $day
     * @return mixed
     */
    public function view(User $user, Day $day)
    {
        return true;
    }

    /**
     * Determine whether the user can create days.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the day.
     *
     * @param  \App\User  $user
     * @param  \App\Day  $day
     * @return mixed
     */
    public function update(User $user, Day $day)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the day.
     *
     * @param  \App\User  $user
     * @param  \App\Day  $day
     * @return mixed
     */
    public function delete(User $user, Day $day)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can restore the day.
     *
     * @param  \App\User  $user
     * @param  \App\Day  $day
     * @return mixed
     */
    public function restore(User $user, Day $day)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can permanently delete the day.
     *
     * @param  \App\User  $user
     * @param  \App\Day  $day
     * @return mixed
     */
    public function forceDelete(User $user, Day $day)
    {
        return $user->isAdmin;
    }
}
