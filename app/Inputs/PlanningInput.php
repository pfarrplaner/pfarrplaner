<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanningInput extends AbstractInput
{

    public $title = 'Planungstabelle';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-pfarrer-bearbeiten');
    }

    public function setup(Request $request)
    {
        $minDate = Day::orderBy('date', 'ASC')->limit(1)->get()->first();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first();
        $cities = Auth::user()->writableCities;

        $ministries = Participant::all()
            ->pluck('category')
            ->unique()
            ->reject(
                function ($value, $key) {
                    return in_array($value, ['P', 'O', 'M', 'A']);
                }
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

        $reqMinistries = $request->get('ministries') ?: [];
        $ministries = [];
        foreach ($reqMinistries as $ministry) {
            $title = $ministry;
            if ($ministry == 'P') {
                $title = 'Pfarrer*in';
            }
            if ($ministry == 'O') {
                $title = 'Organist*in';
            }
            if ($ministry == 'M') {
                $title = 'Mesner*in';
            }
            if ($ministry == 'A') {
                $title = 'Weitere Beteiligte';
            }
            $ministries[$ministry] = $title;
        }

        $input = $this;
        return view(
            $this->getInputViewName(),
            compact('input', 'city', 'services', 'year', 'locations', 'users', 'ministries')
        );
    }

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
