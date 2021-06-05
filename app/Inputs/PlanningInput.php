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

namespace App\Inputs;

use App\City;
use App\Day;
use App\Location;
use App\Mail\ServiceUpdated;
use App\Participant;
use App\Service;
use App\Subscription;
use App\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class PlanningInput
 * @package App\Inputs
 */
class PlanningInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Planungstabelle';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-pfarrer-bearbeiten') ||
            Auth::user()->can('gd-organist-bearbeiten') ||
            Auth::user()->can('gd-mesner-bearbeiten') ||
            Auth::user()->can('gd-allgemein-bearbeiten');
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function setup(Request $request)
    {
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->writableCities;

        $ministries = $this->getAvailableMinistries(
            Participant::all()
                ->pluck('category')
                ->unique()
        );

        return view(
            $this->getViewName('setup'),
            [
                'input' => $this,
                'minDate' => $minDate,
                'maxDate' => $maxDate,
                'cities' => $cities,
                'ministries' => $ministries,
            ]
        );
    }

    /**
     * @param $reqMinistries
     * @return array
     */
    protected function getAvailableMinistries($reqMinistries)
    {
        $ministries = [];
        foreach ($reqMinistries as $ministry) {
            switch ($ministry) {
                case 'P':
                    if (Auth::user()->can('gd-pfarrer-bearbeiten')) {
                        $ministries[$ministry] = 'Pfarrer*in';
                    }
                    break;
                case 'O':
                    if (Auth::user()->can('gd-organist-bearbeiten')) {
                        $ministries[$ministry] = 'Organist*in';
                    }
                    break;
                case 'M':
                    if (Auth::user()->can('gd-mesner-bearbeiten')) {
                        $ministries[$ministry] = 'Mesner*in';
                    }
                    break;
                case 'A':
                    if (Auth::user()->can('gd-allgemein-bearbeiten')) {
                        $ministries[$ministry] = 'Weitere Beteiligte';
                    }
                    break;
                default:
                    if (Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-freie-dienste-bearbeiten')) {
                        $ministries[$ministry] = $ministry;
                    }
            }
        }
        return $ministries;
    }

    /**
     * @param Request $request
     * @return Application|Factory|View|void
     */
    public function input(Request $request)
    {
        $request->validate(
            [
                'year' => 'int|required',
                'city' => 'int|required',
            ]
        );

        $city = City::find($request->get('city'));
        $locations = Location::where('city_id', $city->id)->get();
        $year = $request->get('year');

        $services = Service::with('day', 'location', 'participants')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->where('city_id', $city->id)
            ->where('days.date', '>=', $year . '-01-01')
            ->where('days.date', '<=', $year . '-12-31')
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();


        $users = User::all();

        $ministries = $this->getAvailableMinistries($request->get('ministries') ?: []);


        $input = $this;
        return view(
            $this->getInputViewName(),
            compact('input', 'city', 'services', 'year', 'locations', 'users', 'ministries')
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function save(Request $request)
    {
        $services = $request->get('service') ?: [];

        foreach ($services as $id => $data) {
            $service = Service::find($id);
            if (null !== $service) {
                // get old data set for comparison
                $service->trackChanges();

                if (isset($data['ministries'])) {
                    // participants first
                    foreach ($data['ministries'] as $ministry => $people) {
                        $participants = [];
                        foreach ($people as $person) {
                            $participant = User::createIfNotExists($person);
                            $participants[$participant]['category'] = $ministry;
                        }
                        $service->ministryParticipants($ministry)->sync($participants);
                    }
                    unset($data['ministries']);
                }


                $service->update($data);
                if ($service->isChanged()) {
                    Subscription::send($service, ServiceUpdated::class);
                }
            }
        }
        return redirect()->route('calendar')->with('success', 'Der Plan wurde gespeichert.');
    }
}
