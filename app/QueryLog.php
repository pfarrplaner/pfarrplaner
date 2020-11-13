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

namespace App;


use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Class QueryLog
 * @package App
 */
class QueryLog
{

    public const SEPARATOR = PHP_EOL;

    protected static $logFile = '';

    /**
     * Get the path to the query log
     * @return string
     */
    public static function file($log = '')
    {
        if (self::$logFile != '') {
            $file = self::$logFile;
        } else {
            $file = self::$logFile = storage_path('/logs/query.log'.($log ? '.'.$log : ''));
        }
        return $file;
    }

    /**
     * Put a query into the query log
     * @param $query
     */
    public static function put($query)
    {
        File::append(self::file(), time() . '|||||||' . Crypt::encryptString($query) . self::SEPARATOR);
    }

    /**
     * Get all stored queries
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function all($log = '')
    {
        if (!File::exists(self::file($log))) {
            return [];
        }

        $lines = explode(self::SEPARATOR, File::get(self::file()));
        $queries = [];
        foreach ($lines as $line) {
            if (trim($line)) {
                $t = explode('|||||||', $line);
                $queries[] = [
                    'time' => Carbon::createFromTimestamp($t[0]),
                    'query' => Crypt::decryptString($t[1])
                ];
            }
        }
        return $queries;
    }

    /**
     * Register the service
     */
    public static function register()
    {
        // Query logging
        /**
         * Log all queries except select
         */
        DB::listen(
            function ($query) {
                $bindings = $query->bindings ?? [];
                $normalizedBindings = array_map(
                    function ($binding) {
                        if (is_a($binding, \DateTime::class) || is_a($binding, Carbon::class)) {
                            return '"'.$binding->format('Y-m-d H:i:s').'"';
                        }
                        if (is_object($binding) && method_exists($binding, '__toString')) {
                            return '"'.$binding->__toString().'"';
                        }
                        return is_string($binding) ? '"' . $binding . '"' : $binding;
                    },
                    $bindings
                );
                $sql = Str::replaceArray('?', $normalizedBindings, $query->sql);
                $keyword = explode(' ', $sql)[0];
                if ($keyword !== 'select') {
                    self::put($sql);
                }
            }
        );
    }

    /**
     * Clear the query log
     */
    public static function clear()
    {
        File::delete(self::file());
        File::append(self::file(), '');
    }

    /**
     * Get the date of the last query
     * @return Carbon
     */
    public static function date()
    {
        return Carbon::createFromTimestamp(filemtime(self::file()));
    }

}
