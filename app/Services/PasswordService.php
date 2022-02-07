<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

class PasswordService
{
    private const VOWELS = 'aeiouy';
    private const CONSONANTS = 'bcdfghjklmnpqrstvwxz';
    private const NUMBERS = '0123456789';

    public static function randomPassword()
    {
        return strtoupper(self::randomCharsFromString(self::CONSONANTS))
            .self::randomCharsFromString(self::VOWELS)
            .self::randomCharsFromString(self::CONSONANTS)
            .self::randomCharsFromString(self::VOWELS)
            .self::randomCharsFromString(self::NUMBERS, 4);

    }

    private static function randomCharsFromString($template, $number = 1) {
        $s = '';
        for ($i=0; $i<$number; $i++) {
            $s .= $template[rand(0, strlen($template)-1)];
        }
        return $s;
    }
}
