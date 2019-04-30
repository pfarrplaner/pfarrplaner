<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Spatie\Permission\Models\Role;

class RolePolicy
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
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \Spatie\Permission\Models\Role  $role
     * @return mixed
     */
    public function view(User $user, Role $role)
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
        return $user->hasPermissionTo('rollen-bearbeiten');
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \Spatie\Permission\Models\Role  $role
     * @return mixed
     */
    public function update(User $user, Role $role)
    {
        if ($role->name == 'Superadministrator*in') return $user->hasRole('Superadministrator*in');
        if ($role->name == 'Administrator*in') return $user->hasRole('Superadministrator*in');
        return $user->hasPermissionTo('rollen-bearbeiten');
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \Spatie\Permission\Models\Role  $role
     * @return mixed
     */
    public function delete(User $user, Role $role)
    {
        if ($role->name == 'Superadministrator*in') return $user->hasRole('Superadministrator*in');
        if ($role->name == 'Administrator*in') return $user->hasRole('Superadministrator*in');
        return $user->hasPermissionTo('rollen-bearbeiten');
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \Spatie\Permission\Models\Role  $role
     * @return mixed
     */
    public function restore(User $user, Role $role)
    {
        if ($role->name == 'Superadministrator*in') return $user->hasRole('Superadministrator*in');
        if ($role->name == 'Administrator*in') return $user->hasRole('Superadministrator*in');
        return $user->hasPermissionTo('rollen-bearbeiten');
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \Spatie\Permission\Models\Role  $role
     * @return mixed
     */
    public function forceDelete(User $user, Role $role)
    {
        if ($role->name == 'Superadministrator*in') return $user->hasRole('Superadministrator*in');
        if ($role->name == 'Administrator*in') return $user->hasRole('Superadministrator*in');
        return $user->hasPermissionTo('rollen-bearbeiten');
    }
    
}
