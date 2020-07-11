<?php
/**
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

namespace App\Inputs;

use App\City;
use App\Location;
use App\Mail\ServiceUpdated;
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
 * Class OrganistsInput
 * @package App\Inputs
 */
class OrganistsInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Organistenplan';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-organist-bearbeiten');
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

        $services = Service::with('day', 'location')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->where('city_id', $city->id)
            ->where('days.date', '>=', $year . '-01-01')
            ->where('days.date', '<=', $year . '-12-31')
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();


        $users = User::all();

        $input = $this;
        return view($this->getInputViewName(), compact('input', 'city', 'services', 'year', 'locations', 'users'));
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

            // get old data set for comparison
            $oldInfo = $service->participantsText('O') . $service->description;
            $original = clone $service;
            foreach (['P', 'O', 'M', 'A'] as $key) {
                $originalParticipants[$key] = $original->participantsText($key);
            }

            // participants first
            $participants = [];
            foreach ((isset($data['participants']) ? $data['participants'] : []) as $category => $participantList) {
                foreach ($participantList as $participant) {
                    if ((!is_numeric($participant)) || (User::find($participant) === false)) {
                        $user = new User(
                            [
                                'name' => $participant,
                                'office' => '',
                                'phone' => '',
                                'address' => '',
                                'preference_cities' => '',
                                'first_name' => '',
                                'last_name' => '',
                                'title' => '',
                            ]
                        );
                        $user->save();
                        $participant = $user->id;
                    }
                    $participants[$category][$participant] = ['category' => $category];
                }
            }

            $service->organists()->sync(isset($participants['O']) ? $participants['O'] : []);

            if (null !== $service) {
                $service->description = $data['description'] ?: '';
                $service->save();

                if ($oldInfo != $service->participantsText('O') . $service->description) {
                    Subscription::send($service, ServiceUpdated::class, compact('original', 'originalParticipants'));
                }
            }
        }
        return redirect()->route('calendar')->with('success', 'Der Opferplan wurde gespeichert.');
    }


}
