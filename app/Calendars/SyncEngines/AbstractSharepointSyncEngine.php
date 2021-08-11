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


use App\CalendarConnection;
use App\Calendars\SharePoint\SharePointCalendar;
use Illuminate\Support\Facades\Log;

/**
 * Class AbstractSharepointSyncEngine
 * @package App\Calendars\SyncEngines
 */
class AbstractSharepointSyncEngine extends AbstractSyncEngine
{
    /** @var SharePointCalendar */
    protected $calendar = null;

    public function __construct(CalendarConnection $calendarConnection)
    {
        parent::__construct($calendarConnection);
        $this->setCalendar(
            new SharePointCalendar(
                substr($calendarConnection->connection_string, 4),
                $calendarConnection->credentials1,
                $calendarConnection->credentials2,
                $this->getWSDLUrl()
            )
        );
        $this->matchList();
    }

    protected function matchList() {
        $path = parse_url(substr($this->calendarConnection->connection_string, 4))['path'];
        Log::debug('Sharepoint path is '.$path);
        foreach($this->calendar->getApi()->getLists() as $list) {
            if ($list['defaultviewurl'] == $path) {
                $this->calendar->setListName($list['title']);
                Log::debug('Found corresponding SharePoint list "'.$list['title'].'"');
                return;
            }
        };
        Log::error('No corresponding SharePoint list found for CalendarConnection #'.$this->calendarConnection->id);

    }

    protected function getWSDLUrl()
    {
        return null;
    }




}
