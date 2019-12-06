<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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

namespace App\Inputs;

class Inputs
{

    public static function all() {
        $inputs = [];
        foreach (glob(app_path('Inputs').'/*Input.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $inputClass = 'App\\Inputs\\'.pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($inputClass)) {
                    $input = new $inputClass();
                    if ($input->canEdit()) $inputs[$input->title] = $input;
                }
            }
        }
        ksort($inputs);
        return $inputs;
    }

    public static function get($input): AbstractInput {
        $inputClass = 'App\\Inputs\\' . ucfirst($input) . 'Input';
        if (class_exists($inputClass)) {
            $input = new $inputClass();
            return $input;
        }
        return null;
    }
}
