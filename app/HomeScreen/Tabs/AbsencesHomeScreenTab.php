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


use App\Absence;
use App\Replacement;
use App\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AbsencesHomeScreenTab extends AbstractHomeScreenTab
{
    protected $title = 'Mein Urlaub';
    protected $description = 'Zeigt den eigenen Urlaub und andere Abwesenheiten';
    protected $absenceQuery;
    protected $replacementQuery;

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
        $data['absences'] = $this->absenceQuery->get();
        $data['replacements'] = $this->replacementQuery->get();
        return parent::getContent($data);
    }

    public function toArray($data = [])
    {
        $data['absences'] = $this->absenceQuery->get();
        $data['replacements'] = $this->replacementQuery->get();
        $data['count'] = count($data['absences']) + count($data['replacements']);
        return parent::toArray($data);
    }

    /**
     * Build the queries
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function buildQueries() {
        $start = Carbon::now()->setTime(0, 0, 0);
        $end = Carbon::now()->addMonth(2);

        $this->absenceQuery = Absence::where('user_id', Auth::user()->id)->where('to', '>=', now());
        $this->replacementQuery = Replacement::with('absence')
            ->whereHas(
                'users',
                function ($query) {
                    $query->where('users.id', Auth::user()->id);
                }
            )
            ->where('to', '>=', now())
            ->orderBy('from')
            ->orderBy('to');
    }


}
