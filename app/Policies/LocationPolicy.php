<?php

namespace App\Policies;

use App\User;
use App\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    public function index(User $user) {
        if ($user->hasRole('Administrator*in')) return true;
        if ($user->can('ort-bearbeiten') || $user->can('gd-opfer-bearbeiten')) return true;
        return false;
    }

    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function view(User $user, Location $location)
    {
        return true;
    }

    /**
     * Determine whether the user can create locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function update(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function delete(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can restore the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function restore(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }

    /**
     * Determine whether the user can permanently delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function forceDelete(User $user, Location $location)
    {
        if ($user->hasRole('Administrator*in') && $location->city->administeredBy($user)) return true;
        if ($user->can('kirche-bearbeiten') && $user->writableCities->contains($location->city)) return true;
        return false;
    }
}
