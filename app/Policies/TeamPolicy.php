<?php

namespace App\Policies;

use App\Team;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
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

    public function index(User $user)
    {
        return count($user->writableCities) > 0;
    }

    public function create(User $user)
    {
        return count($user->writableCities) > 0;
    }

    public function update(User $user, Team $team)
    {
        return $user->writableCities->pluck('id')->contains($team->city_id);
    }

    public function delete(User $user, Team $team)
    {
        return $user->writableCities->pluck('id')->contains($team->city_id);
    }
}
