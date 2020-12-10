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


use App\Service;

class CasesHomeScreenTab extends AbstractHomeScreenTab
{

    protected $title = 'Kasualien';
    protected $description = 'Zeigt die Kasualienplanung für eine Auswahl von Gemeinden';

    protected $queries = [];

    public function __construct($config = [])
    {
        $this->setDefaultConfig($config, ['includeCities' => [], 'title' => 'Kasualien']);
        parent::__construct($config);
        $this->buildQueries();
    }

    public function getTitle(): string
    {
        return $this->config['title'] ?: $this->title;
    }

    public function getContent($data = [])
    {
        foreach (['funerals', 'weddings', 'baptisms'] as $item) {
            $data[$item] = $this->queries[$item]->get();
        }
        return parent::getContent($data);
    }

    protected function buildQueries()
    {
        $cities = (is_array($this->config['includeCities']) ? $this->config['includeCities'] : [$this->config['includeCities']]);
        $this->queries['funerals'] = Service::with(['funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $cities)
            ->whereHas('funerals')
            ->orderBy('days.date', 'DESC')
            ->orderBy('time', 'DESC')
            ->limit(10);

        $this->queries['weddings'] = Service::with(['weddings', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $cities)
            ->whereHas('weddings')
            ->orderBy('days.date', 'DESC')
            ->orderBy('time', 'DESC')
            ->limit(10);

        $this->queries['baptisms'] = Service::with(['baptisms', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereIn('city_id', $cities)
            ->whereHas('baptisms')
            ->orderBy('days.date', 'DESC')
            ->orderBy('time', 'DESC')
            ->limit(10);
    }

}
