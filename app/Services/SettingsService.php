<?php
/*
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

namespace App\Services;


use App\User;
use App\UserSetting;

class SettingsService
{

    protected $settings = [];

    protected function cacheSettings (User $user) {
        // cache all settings, if not done yet
        if (!is_array($this->settings) || (!isset($this->settings[$user->id])) || (count($this->settings[$user->id]) == 0)) {
            $this->settings[$user->id] = UserSetting::where('user_id', $user->id)->get()->mapWithKeys(function($item){
                return [$item['key'] => $item];
            });
        }
    }

    /**
     * @param string $key
     * @param null $default
     * @param bool $returnObject
     * @param bool $unserialize
     * @return UserSetting|mixed
     */
    public function get(User $user, string $key, $default = null, $returnObject = false) {
        // need to trick error reporting or else this will fail with an E_NOTICE
        $err = error_reporting();
        error_reporting(0);

        $this->cacheSettings($user);

        if (!isset($this->settings[$user->id][$key])) {
            $setting = new UserSetting(
                [
                    'user_id' => $user->id,
                    'key' => $key,
                    'value' => $default,
                ]
            );
        } else {
            $setting = $this->settings[$user->id][$key];
        }
        $return = ($returnObject ? $setting : $setting->value);
        error_reporting($err);
        return $return;

    }

    public function has(User $user, string $key): bool {
        $this->cacheSettings($user);
        return isset($this->settings[$user->id][$key]);
    }

    public function set(User $user, string $key, $value): UserSetting {
        $this->cacheSettings($user);
        if (is_array($value)) $value = '_____'.serialize($value);
        if ($this->has($user, $key)) {
            $setting = $this->get($user, $key, null, true);
            $setting->value = $value;
            $setting->save();
        } else {
            $setting = UserSetting::create(
                [
                    'user_id' => $user->id,
                    'key' => $key,
                    'value' => $value,
                ]
            );
        }
        return $setting;
    }

    public function all(User $user, $forceReload = false)
    {
        if ($forceReload) unset($this->settings[$user->id]);
        $this->cacheSettings($user);
        return $this->settings[$user->id] ? $this->settings[$user->id]->map(function($item){
            return $item->value;
        }) : [];
    }


}
