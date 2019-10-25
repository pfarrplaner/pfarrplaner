<?php

namespace App\Policies;

use App\Absence;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbsencePolicy
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

    public function create(User $user) {
        return $user->manage_absences || $user->hasPermissionTo('fremden-urlaub-bearbeiten');
    }

    public function update(User $user, Absence $absence) {
        if ($user->id == $absence->user->id) return true;
        if ($user->hasPermissionTo('fremden-urlaub-bearbeiten')) {
            $cityIds = $absence->user->cities->pluck('id');
            foreach ($user->writableCities as $city) {
                if ($cityIds->contains($city->id)) return true;
            }
        }
        return false;
    }

}
