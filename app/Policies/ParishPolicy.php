<?php

namespace App\Policies;

use App\Parish;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParishPolicy
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

    public function index(User $user) {
        if ($user->hasRole('Administrator*in')) return true;
        return false;
    }

    public function create(User $user, Parish $model) {
        if ($user->adminCities->contains($model->owningCity)) return true;
        return false;
    }

    public function update(User $user, Parish $model) {
        if ($user->adminCities->contains($model->owningCity)) return true;
        return false;
    }

    public function delete(User $user, Parish $model) {
        if ($user->adminCities->contains($model->owningCity)) return true;
        return false;
    }

    public function restore(User $user, Parish $model) {
        return false;
    }

    public function forceDelete(User $user, Parish $model) {
        return false;
    }
}
