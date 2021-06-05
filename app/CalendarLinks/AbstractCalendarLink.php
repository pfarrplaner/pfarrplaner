<?php
/**
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

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 13:29
 */

namespace App\CalendarLinks;


use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

/**
 * Class AbstractCalendarLink
 * @package App\CalendarLinks
 */
class AbstractCalendarLink
{
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var string
     */
    protected $viewName = 'ical';

    /** @var User $user */
    protected $user = null;


    public function __construct()
    {
        $this->data['key'] = $this->getKey();
        if ($x = request()->get('includeHidden', 0)) $this->data['includeHidden'] = $x;
        if (null !== Auth::user()) {
            $this->user = Auth::user();
            $this->data['token'] = Auth::user()->getToken();
            $this->data['user'] = Auth::user()->id;
        }
    }

    public function getKey(): string
    {
        return lcfirst(strtr(get_class($this), ['CalendarLink' => '', 'App\\s\\' => '', 'App\\CalendarLinks\\' => '']));
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function setupRoute()
    {
        return route('ical.setup', ['key' => $this->getKey(), 'includeHidden' => request()->get('includeHidden', 0)]);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return Application|Factory|\Illuminate\View\View
     */
    public function setupView()
    {
        $data = $this->setupData();
        $data['calendarLink'] = $this;
        return view($this->getSetupViewName(), $data);
    }

    /**
     * @return array
     */
    public function setupData()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getSetupViewName()
    {
        return 'ical.' . $this->getKey() . '.setup';
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return route('ical.export', $this->data);
    }

    /**
     * @param Request $request
     * @param User $user
     * @return string|string[]|null
     */
    public function export(Request $request, User $user)
    {
        $this->setDataFromRequest($request);
        $this->setUser($user);
        $data = $this->getRenderData($request, $user);
        $calendarLink = $this;
        $raw = View::make('ical.export.' . $this->viewName, compact('calendarLink', 'data'));
        $s = str_replace(
            "\r\n\r\n",
            "\r\n",
            str_replace(
                '@@@@',
                "\r\n",
                str_replace(
                    "\n",
                    "\r\n",
                    str_replace("\r\n", '@@@@', str_replace(' ,', ',', $raw))
                )
            )
        );
        return preg_replace('/^(\s*)/m', '', $s);
    }

    /**
     * @param Request $request
     */
    public function setDataFromRequest(Request $request)
    {
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->data['user'] = $this->user;
        $this->data['token'] = $this->user->getToken();
    }

    /**
     * @param Request $request
     * @param User $user
     * @return array
     */
    public function getRenderData(Request $request, User $user)
    {
        return [];
    }


}
