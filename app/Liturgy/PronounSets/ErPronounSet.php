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

namespace App\Liturgy\PronounSets;


class ErPronounSet extends AbstractPronounSet
{

    protected $label = 'er/sein/ihm/ihn';

    protected $pronouns = [
        'er' => 'er',
        'seiner' => 'seiner',
        'ihm' => 'ihm',
        'ihn' => 'ihn',
        // possessive
        'sein' => 'sein',
        'seine' => 'seine',
        'seines' => 'seines',
        'seinem' => 'seinem',
        'seinen' => 'seinen',
        // relative
        'der' => 'der',
        'dessen' => 'dessen',
        'dem' => 'dem',
        'den' => 'den',
        // demonstrative
        'dieser' => 'dieser',
        'welcher' => 'welcher',
        // misc
        'Sohn' => 'Sohn',
        'Bruder' => 'Bruder',
        'Mann' => 'Mann',
        'deinem Ehemann' => 'deinem Ehemann',
        'Ehemann' => 'Ehemann',
        'Ehegatte' => 'Ehegatte',
        'Verstorbener' => 'Verstorbene',
        'der Verstorbene' => 'der Verstorbene',
        'des Verstorbenen' => 'des Verstorbenen',
        'dem Verstorbenen' => 'dem Verstorbenen',
        'den Verstorbenen' => 'den Verstorbenen',
    ];

}
