<?php
/*
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

namespace App\HomeScreen\Tabs;


use App\Absence;
use App\Replacement;
use App\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsenceRequestsHomeScreenTab extends AbstractHomeScreenTab
{
    protected $title = 'Urlaubsanträge';
    protected $description = 'Zeigt zu bearbeitende Urlaubsanträge';
    protected $checkQuery;
    protected $approveQuery;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->buildQueries();
    }

    public function getTitle(): string
    {
        return parent::getTitle();
    }

    public function getContent($data = [])
    {
        $data['check'] = $this->checkQuery->get();
        $data['approve'] = $this->approveQuery->get();
        return parent::getContent($data);
    }

    public function toArray($data = [])
    {
        $data['check'] = $this->checkQuery->get();
        $data['approve'] = $this->approveQuery->get();
        $data['count'] = count($data['check']) + count($data['approve']);
        return parent::toArray($data);
    }

    /**
     * Build the queries
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQueries() {
        $start = Carbon::now()->setTime(0, 0, 0);
        $end = Carbon::now()->addMonth(2);

        $this->checkQuery = Absence::whereHas('user', function($query) {
            $query->whereHas('vacationAdmins', function ($query2) {
                $query2->where('related_user_id', Auth::user()->id);
            })->where('workflow_status', 0);
        });
        $this->approveQuery = Absence::with(['checkedBy'])
            ->whereHas('user', function($query) {
            $query->whereHas('vacationApprovers', function ($query2) {
                $query2->where('related_user_id', Auth::user()->id);
            })->where('workflow_status', 1);
        });
    }


}
