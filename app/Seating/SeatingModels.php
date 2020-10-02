<?php
/*
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

namespace App\Seating;


use League\Flysystem\Adapter\AbstractAdapter;

class SeatingModels
{


    public static function all() {
        $seatingModels = [];
        foreach (glob(app_path('Seating') . '/*SeatingModel.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $seatingModelClass = 'App\\Seating\\' . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($seatingModelClass)) {
                    /** @var AbstractSeatingModel $seatingModel */
                    $seatingModel = new $seatingModelClass();
                    $seatingModels[$seatingModel->getTitle()] = $seatingModel;
                }
            }
        }
        ksort($seatingModels);
        return $seatingModels;
    }

    public static function select() {
        $seatingModels = [];
        foreach (self::all() as $title => $class) {
            $seatingModels[get_class($class)] = $title;
        }
        return $seatingModels;
    }

    public static function byTitle($title) {
        /** @var AbstractSeatingModel $class */
        foreach (self::all() as $class) {
            if ($class->getTitle() == $title) return $class;
        }
        return null;
    }

}
