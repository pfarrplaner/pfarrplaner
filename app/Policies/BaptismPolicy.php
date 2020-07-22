<?php

namespace App\Policies;

use App\Baptism;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BaptismPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param Baptism $baptism
     * @return bool
     */
    protected function hasCityPermission(User $user, Baptism $baptism): bool
    {
        return $user->writableCities->pluck('id')->contains($baptism->city_id);
    }

    /**
     * @param User $user
     * @param Baptism $baptism
     * @return bool
     */
    protected function mayChange(User $user, Baptism $baptism): bool
    {
        if (!$user->hasPermissionTo('gd-bearbeiten')) {
            return false;
        }
        if (null === $baptism) {
            return true;
        }
        return $this->hasCityPermission($user, $baptism);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('gd-bearbeiten');
    }

    /**
     * @param User $user
     * @param Baptism|null $baptism
     * @return bool
     */
    public function update(User $user, Baptism $baptism = null): bool
    {
        return $this->mayChange($user, $baptism);
    }

    /**
     * @param User $user
     * @param Baptism|null $baptism
     * @return bool
     */
    public function delete(User $user, Baptism $baptism = null): bool
    {
        return $this->mayChange($user, $baptism);
    }
}

