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


use View;

class AbstractHomeScreenTab
{

    protected $title = '';
    protected $description = '';
    protected $config = [];

    public function __construct($config = [])
    {
        if (!count($config)) $this->setConfig($config);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return array|mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array|mixed $config
     */
    public function setConfig($config): void
    {
        if (isset($config[$this->getKey()])) {
            $this->config = $config[$this->getKey()];
        } else {
            $this->config = $config;
        }
    }


    protected function setDefaultConfig(array $config, array $defaults) {
        if (isset($config[$this->getKey()])) $config = $config[$this->getKey()];
        foreach ($defaults as $key => $val) {
            if (!isset($config[$key])) $config[$key] = $val;
        }
        $this->setConfig($config);
    }


    /**
     * Get the class's key
     * @return string
     */
    public function getKey(): string {
        return lcfirst(basename(str_replace('\\', '/', get_called_class()), 'HomeScreenTab'));
    }

    public function viewName($viewName)
    {
        return ('homescreen.tabs.'.$this->getKey().'.'.$viewName);
    }

    /**
     * Render a specific view
     * @return string|Illuminate\Contracts\View\View Rendered view
     */
    public function view($viewName, $data = []) {
        $viewName = $this->viewName($viewName);
        $data = array_merge($data, ['tab' => $this, 'config' => $this->config]);
        if (View::exists($viewName)) {
            return View::make($viewName, $data);
        } else {
            if ($viewName == 'config') {
                return '<i>Für diesen Reiter gibt es nichts einzustellen.</i>';
            } else {
                return '';
            }
        }
    }

    /**
     * Render the tab header
     * @param array $data
     * @return string|Illuminate\Contracts\View\View
     */
    public function getHeader($data = []) {
        return $this->view('tabHeader', $data);
    }

    /**
     * Render the tab content
     * @return string|Illuminate\Contracts\View\View
     */
    public function getContent($data = []) {
        return $this->view('tab', $data);
    }

    /**
     * Get the name of a config key
     * @param string $name
     * @param bool $isArray
     * @return string
     */
    public function configKey($name = '', bool $isArray = false)
    {
        return 'settings[homeScreenTabsConfig]['.$this->getKey().']'.($name ? '['.$name.']' : '').($isArray ? '[]' : '');
    }

    public function toArray($data = [])
    {
        $data['title'] = $this->getTitle();
        $data['description'] = $this->getDescription();
        $data['key'] = $this->getKey();
        return $data;
    }

}
