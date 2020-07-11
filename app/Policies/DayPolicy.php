<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use App\Day;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DayPolicy
 * @package App\Policies
 */
class DayPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the day.
     *
     * @param User $user
     * @param Day $day
     * @return mixed
     */
    public function view(User $user, Day $day)
    {
        return true;
    }

    /**
     * Determine whether the user can create days.
     *
     * @param User $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the day.
     *
     * @param User $user
     * @param Day $day
     * @return mixed
     */
    public function update(User $user, Day $day)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the day.
     *
     * @param User $user
     * @param Day $day
     * @return mixed
     */
    public function delete(User $user, Day $day)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can restore the day.
     *
     * @param User $user
     * @param Day $day
     * @return mixed
     */
    public function restore(User $user, Day $day)
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can permanently delete the day.
     *
     * @param User $user
     * @param Day $day
     * @return mixed
     */
    public function forceDelete(User $user, Day $day)
    {
        return $user->isAdmin;
    }
}
