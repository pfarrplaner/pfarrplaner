<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Liturgy\PronounSets;


class PronounSets
{

    public static function all() {
        $items = [];
        foreach (glob(app_path('Liturgy/PronounSets/*PronounSet.php')) as $file) {
            $short = pathinfo($file, PATHINFO_FILENAME);
            if (substr($short, 0, 8) != 'Abstract') {
                $class = '\\App\\Liturgy\\PronounSets\\'.$short;
                $items[] = new $class();
            }
        }
        return $items;
    }

    public static function items() {
        $items = [];
        /** @var AbstractPronounSet $item */
        foreach (self::all() as $item) {
            $items[$item->getLabel()] = $item->getKey();
        }
        return $items;
    }

    public static function get($key) {
        $class = '\\App\\Liturgy\\PronounSets\\'.ucfirst($key).'PronounSet';
        if (!class_exists($class)) return null;
        return new $class();
    }

    /**
     * @return array
     */
    public static function toArray() {
        $pronounSets = [];
        /** @var AbstractPronounSet $pronounSet */
        foreach (self::all() as $pronounSet) {
            $pronounSets[] = ['key' => $pronounSet->getKey(), 'label' => $pronounSet->getLabel()];
        };
        return $pronounSets;
    }

}
