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
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MissingEntriesHomeScreenTab extends AbstractHomeScreenTab
{
    protected $title = 'Fehlende Einträge';
    protected $description = 'Zeigt Gottesdienste mit fehlenden Einträgen';
    protected $missing = [];

    public function __construct($config = [])
    {
        // preset default config
        $this->setDefaultConfig($config, ['ministries' => []]);
        parent::__construct($config);
//        if (!is_array($this->config['ministries'])) $this->config['ministries'] = [$this->config['ministries']];
        $this->missing = $this->getMissing();
    }

    public function getCount()
    {
        return count($this->missing);
    }

    public function getContent($data = [])
    {
        $data['missing'] = $this->missing;
        return parent::getContent($data);
    }

    /**
     * Build the query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getMissing() {
        $missing = Service::withOpenMinistries($this->config['ministries']);
        return $missing;
    }

}
