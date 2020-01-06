<?php

namespace App\Policies;

use App\Providers\AuthServiceProvider;
use App\User;
use App\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServicePolicy
{
    use HandlesAuthorization;

    protected function hasCityPermission($user, $service) {
        return $user->writableCities->pluck('id')->contains($service->city_id);
    }

    /**
     * Determine whether the user can view the service.
     *
     * @param  \App\User  $user
     * @param  \App\Service  $service
     * @return mixed
     */
    public function view(User $user, Service $service)
    {
        return true;
    }

    /**
     * Determine whether the user can create services.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('gd-bearbeiten');
    }

    /**
     * Determine whether the user can update the service.
     *
     * @param  \App\User  $user
     * @param  \App\Service  $service
     * @return mixed
     */
    public function update(User $user, Service $service)
    {
        if ($user->hasRole(AuthServiceProvider::ADMIN) && $this->hasCityPermission($user, $service)) return true;
        if ($user->hasPermissionTo('gd-bearbeiten') && $this->hasCityPermission($user, $service)) return true;
        return false;
    }

    /**
     * Determine whether the user can delete the service.
     *
     * @param  \App\User  $user
     * @param  \App\Service  $service
     * @return mixed
     */
    public function delete(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }

    /**
     * Determine whether the user can restore the service.
     *
     * @param  \App\User  $user
     * @param  \App\Service  $service
     * @return mixed
     */
    public function restore(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }

    /**
     * Determine whether the user can permanently delete the service.
     *
     * @param  \App\User  $user
     * @param  \App\Service  $service
     * @return mixed
     */
    public function forceDelete(User $user, Service $service)
    {
        return $user->hasPermissionTo('gd-allgemein-bearbeiten') && $this->hasCityPermission($user, $service);
    }
}
