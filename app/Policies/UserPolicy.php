<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function index(User $user, User $model) {
        return $user->hasPermissionTo('benutzerliste-lokal-sehen') || $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can choose another user to merge the two
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function join(User $user, User $model) {
        return $user->hasPermissionTo('benutzer-bearbeiten');
    }

    /**
     * Determine whether the user can merge this user into another one
     *
     * @param  \App\User  $user
     * @param  \App\User  $model
     * @return mixed
     */
    public function doJoin(User $user, User $model) {
        return true;
    }


    /**
     * Determine wether the user can edit another users absences
     * @param User $user
     * @param User $model
     */
    public function editAbsences (User $user, User $model) {
        if ($user->id == $model->id) return true;
        if ($user->hasPermissionTo('fremden-urlaub-bearbeiten')) {
            if (!$model->hasRole('Pfarrer*in')) {
                if (count($user->writableCities->intersect($model->homeCities))) return true;
            }
        }
        return false;
    }

}

