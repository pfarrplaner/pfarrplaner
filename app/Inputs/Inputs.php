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

namespace App\Inputs;

use Exception;

/**
 * Class Inputs
 * @package App\Inputs
 */
class Inputs
{

    /**
     * @return array
     */
    public static function all()
    {
        $inputs = collect();
        foreach (glob(app_path('Inputs') . '/*Input.php') as $file) {
            if (substr(pathinfo($file, PATHINFO_FILENAME), 0, 8) !== 'Abstract') {
                $inputClass = 'App\\Inputs\\' . pathinfo($file, PATHINFO_FILENAME);
                if (class_exists($inputClass)) {
                    $input = new $inputClass();
                    if ($input->canEdit()) {
                        $inputs->push([
                            'name' => $input->title,
                            'description' => $input->description,
                            'id' => $input->getKey(),
                        ]);
                    }
                }
            }
        }
        return $inputs->sortBy('name');
    }

    /**
     * @param $input
     * @throws Exception
     * @return AbstractInput
     */
    public static function get($input): AbstractInput
    {
        $inputClass = 'App\\Inputs\\' . ucfirst($input) . 'Input';
        if (!class_exists($inputClass)) throw new Exception ('Input class '.$inputClass.' does not exist.');
        return new $inputClass();
    }
}
