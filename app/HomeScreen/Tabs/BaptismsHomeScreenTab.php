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

namespace App\HomeScreen\Tabs;


use App\Baptism;
use App\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BaptismsHomeScreenTab extends AbstractHomeScreenTab
{
    protected $title = 'Taufen';
    protected $description = 'Zeigt die anstehenden Taufen';
    protected $baptismQuery = null;
    protected $baptismRequestQuery = null;

    public function __construct($config = [])
    {
        // preset default config
        $this->setDefaultConfig($config, ['mine' => 0, 'showRequests' => 0]);

        parent::__construct($config);
        $this->buildQueries();
    }

    public function getTitle(): string
    {
        if ($this->config['mine']) return 'Meine Taufen';
        return parent::getTitle();
    }

    public function getBaptismCount()
    {
        return $this->baptismQuery->count();
    }

    public function getBaptismRequestCount()
    {
        return $this->baptismRequestQuery->count();
    }

    public function getContent($data = [])
    {
        $data['baptisms'] = $this->baptismQuery->get()->load('day');
        $data['baptismRequests'] = $this->baptismRequestQuery->get();
        return parent::getContent($data);
    }

    /**
     * Build the query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQueries() {
        $start = Carbon::now()->setTime(0, 0, 0);
        $end = Carbon::now()->addMonth(2);

        $this->baptismQuery = Service::with(['baptisms', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', Auth::user()->writableCities->pluck('id'))
            ->whereHas('baptisms')
            ->whereHas(
                'day',
                function ($query) use ($start, $end) {
                    $query->where('date', '>=', $start)
                        ->where('date', '<=', $end);
                }
            )
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC');

        if ($this->config['mine']) {
            $this->baptismQuery->whereHas(
                'participants',
                function ($query) {
                    $query->where('user_id', Auth::user()->id);
                }
            );
        }

        $this->baptismRequestQuery = Baptism::whereNull('service_id')
            ->whereIn('city_id', Auth::user()->writableCities->pluck('id'));

    }

}
