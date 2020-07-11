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

/**
 * src/config/database-encryption.php.
 *
 * @author      Austin Heap <me@austinheap.com>
 * @version     v0.2.0
 */

return [

    /*
     * Enable database encryption.
     *
     * Default: false
     *
     * @var null|bool
     */
    'enabled'            => env('DB_ENCRYPTION_ENABLED', true),

    /*
     * Prefix used in attribute header.
     *
     * Default: __LARAVEL-DATABASE-ENCRYPTED-%VERSION%__
     *
     * @var null|string
     */
    'prefix'             => env('DB_ENCRYPTION_PREFIX', '__LARAVEL-DATABASE-ENCRYPTED-%VERSION%__'),

    /*
     * Enable header versioning.
     *
     * Default: true
     *
     * @var null|bool
     */
    'versioning'         => env('DB_ENCRYPTION_VERSIONING', false),

    /*
     * Control characters used by header.
     *
     * Default: [
     *     'header' => [
     *         'start'      => 1, // or: chr(1)
     *         'stop'       => 4, // or: chr(4)
     *     ],
     *     'prefix' => [
     *         'start'      => 2, // or: chr(2)
     *         'stop'       => 3, // or: chr(3)
     *     ],
     *     'field'   => [
     *         'start'      => 30, // or: chr(30)
     *         'delimiter'  => 25, // or: chr(25)
     *         'stop'       => 23, // or: chr(23)
     *     ],
     * ]
     *
     * @var null|array
     */
    'control_characters' => null,

];
