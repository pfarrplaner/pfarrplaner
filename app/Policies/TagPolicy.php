<?php

namespace App\Policies;

use App\Tag;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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
        return false;
    }

    public function create(User $user, Tag $model)
    {
        return false;
    }

    public function update(User $user, Tag $model)
    {
        return false;
    }

    public function delete(User $user, Tag $model)
    {
        return false;
    }

    public function forceDelete(User $user, Tag $model)
    {
        return false;
    }

    public function restore(User $user, Tag $model)
    {
        return false;
    }
}
