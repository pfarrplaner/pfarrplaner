<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Liturgy\LiturgySheets;


class LiturgySheets
{

    public static function all()
    {
        $sheets = [];
        foreach (glob(app_path('Liturgy/LiturgySheets/*LiturgySheet.php')) as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) != 'Abstract') {
                $class = 'App\\Liturgy\\LiturgySheets\\'.pathinfo($file, PATHINFO_FILENAME);
                /** @var AbstractLiturgySheet $object */
                $object = new $class();
                $sheets[$object->getTitle()] = [
                    'class' => $class,
                    'key' => $object->getKey(),
                    'title' => $object->getTitle(),
                    'icon' => $object->getIcon(),
                    'extension' => $object->getExtension(),
                    'isNotAFile' => $object->isNotAFile(),
                ];
            }
        }
        ksort ($sheets);
        return $sheets;
    }

}
