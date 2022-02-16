<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Policies;

use App\Absence;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AbsencePolicy
 * @package App\Policies
 */
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

    /**
     * @param User $user
     * @param Absence $absence
     * @return bool
     */
    public function view(User $user, Absence $absence) {
        return $this->update($user, $absence);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user, Absence $absence = null)
    {
        if ((null !== $absence) && $this->update($user, $absence)) return true;
        return $user->manage_absences || $user->hasPermissionTo('fremden-urlaub-bearbeiten');
    }

    /**
     * @param User $user
     * @param Absence $absence
     * @return bool
     */
    public function update(User $user, Absence $absence)
    {
        if ($user->id == $absence->user->id) {
            return true;
        }

        // via permission
        if ($user->hasPermissionTo('fremden-urlaub-bearbeiten')) {
            $cityIds = $absence->user->homeCities->pluck('id');
            foreach ($user->writableCities as $city) {
                if ($cityIds->contains($city->id) && (!$absence->user->hasRole('Pfarrer*in'))) {
                    return true;
                }
            }
        }

        // via workflow
        if ($absence->user->vacationAdmins->pluck('id')->contains($user->id)) return true;
        if (($absence->workflow_status > 0) && $absence->user->vacationApprovers->pluck('id')->contains($user->id)) return true;

        return false;
    }

    /**
     * @param User $user
     * @param Absence $absence
     * @return bool
     */
    public function delete(User $user, Absence $absence)
    {
        return $this->update($user, $absence);
    }


    /**
     * Check if the user can manage his own vacation entries
     * @param User $user
     * @return bool
     */
    public function selfAdminister(User $user, Absence $absence = null)
    {
        if ((null !== $absence) && ($absence->user->id != $user->id)) return false;
        return ((0 == $user->vacationAdmins->count()) && (0 == $user->vacationApprovers->count()));
    }




}
