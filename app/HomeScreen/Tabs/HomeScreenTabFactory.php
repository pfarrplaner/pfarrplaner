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

namespace App\HomeScreen\Tabs;


class HomeScreenTabFactory
{

    /**
     * Get homescreen tabs
     * @param array $config
     * @param array $filter
     * @return array
     */
    public static function get(array $config, array $filter = [])
    {
        $homeScreenTabs = [];
        foreach ((glob(app_path('HomeScreen/Tabs/*HomeScreenTab.php'))) as $file) {
            if (basename($file, 'HomeScreenTab.php') != 'Abstract') {
                $key = lcfirst(basename($file, 'HomeScreenTab.php'));
                if (!isset($config[$key])) $config[$key] = [];
                $class = 'App\\HomeScreen\\Tabs\\' . basename($file, '.php');
                /** @var AbstractHomeScreenTab $object */
                $object = new $class($config);
                $homeScreenTabs[$object->getTitle()] = $object;
            }
        }
        ksort($homeScreenTabs);
        $tabs = [];
        foreach ($homeScreenTabs as $tab) {
            $tabs[$tab->getKey()] = $tab;
        }
        if (count($filter)) {
            $allTabs = $tabs;
            $tabs = [];
            foreach ($filter as $tab) {
                if (isset($allTabs[$tab])) $tabs[$tab] = $allTabs[$tab];
            }
        }
        return $tabs;
    }

    /**
     * Get a single tab with contents
     * @param $tab
     * @throws \Exception
     */
    public static function getOne($tab, $tabIndex) {
        $class = 'App\\HomeScreen\\Tabs\\' . ucfirst($tab['type'].'HomeScreenTab');
        if (class_exists($class)) {
            $tabObject = new $class($tab['config']);
        } else {
            throw new \Exception('Class not found: '.$class);
        }
        /** @var AbstractHomeScreenTab $tabObject */
        $result = $tabObject->toArray();
        $result['config'] = $tab['config'];
        $result['key'] = $result['type'].$tabIndex;
        $result['index'] = $tabIndex;
        return $result;
    }

    /**
     * Get all homescreen tabs
     * @param array $config
     * @return array
     */
    public static function all(array $config)
    {
        return self::get($config, []);
    }

    public static function available() {
        $tabs = self::get([]);
        $available = [];
        /**
         * @var  $key
         * @var AbstractHomeScreenTab $tab
         */
        foreach ($tabs as $key =>$tab) {
            $available[$key] = [
                'type' =>  $key,
                'title' => $tab->getTitle(),
                'description' => $tab->getDescription(),
                'config' => $tab->getConfig(),
            ];
        }
        return $available;
    }

}
