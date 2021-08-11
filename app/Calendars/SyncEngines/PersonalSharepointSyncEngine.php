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

namespace App\Calendars\SyncEngines;

/**
 * Class PersonalSharepointSyncEngine
 * @package App\Calendars\SyncEngines
 */
class PersonalSharepointSyncEngine extends AbstractSharepointSyncEngine
{
    public static $prefix = 'spp';

    /**
     * Get the URL of the WSDL file for a Sharepoint resource
     * @return string Url
     * @throws \Exception when connection string is invalid
     */
    protected function getWSDLUrl()
    {
        preg_match(
            '/https:\/\/personal.elkw.de\/privat\/.*?\//',
            substr($this->getCalendarConnection()->connection_string, 4),
            $matches
        );

        if (!isset($matches[0])) {
            throw new \Exception(
                'Invalid connection string: '
                . $this->getCalendarConnection()->connection_string
                . 'for CalendarConnection #' . $this->getCalendarConnection()->id
            );
        }

        return $matches[0] . '_vti_bin/Lists.asmx?WSDL';
    }


}
