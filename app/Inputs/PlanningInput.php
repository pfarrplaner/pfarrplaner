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
use App\Events\ServiceUpdated;
use App\Location;
use App\Participant;
use App\Service;
use App\Subscription;
use App\Team;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

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
        $cities = Auth::user()->writableCities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();

        $ministries = $this->getAvailableMinistries(
            Participant::all()
                ->pluck('category')
                ->unique()
        );

        return Inertia::render('Inputs/Planning/PlanningInputSetup', compact('cities', 'ministries', 'locations'));

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
        \Debugbar::disable();
        $setup = $request->validate(
            [
                'from' => 'required|date_format:d.m.Y',
                'to' => 'required|date_format:d.m.Y',
                'city' => 'required|exists:cities,id',
                'locations.*' => 'nullable|exists:locations,id',
                'ministries.*' => 'required|string',
            ]
        );

        $city = City::find($setup['city']);
        $locations = Location::where('city_id', $city->id)->get();

        $teams = Team::with('users')->where('city_id', $city->id)->get();

         $query = Service::with([])
            ->select('services.slug')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->where('city_id', $city->id)
            ->where('days.date', '>=', Carbon::createFromFormat('d.m.Y', $setup['from']))
            ->where('days.date', '<=', Carbon::createFromFormat('d.m.Y', $setup['to']))
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC');

        if (count($setup['locations'] ?? [])) {
            $query->whereIn('services.location_id', $setup['locations']);
        }

        $serviceSlugs = $query->get()->pluck('slug');

        $users = User::all();

        $ministries = $this->getAvailableMinistries($setup['ministries'] ?: []);


        $input = $this;

        return Inertia::render('Inputs/Planning/PlanningInputForm', compact('serviceSlugs', 'users', 'ministries', 'teams', 'city'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse|void
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'slug' => 'required|string|exists:services,slug',
            'ministries' => 'required',
                                   ]);

        $service = Service::where('slug', $data['slug'])->first();
        $service->trackChanges();
        $originalParticipants = $service->participants;

        // build existing participants table
        $participants = [];
        foreach ($service->participants as $participant) {
            $participants[$participant->pivot->category][$participant->id] = ['category' => $participant->pivot->category];
        }

        // replace with new ministry settings
        foreach ($data['ministries'] as $ministry => $people) {
            $participants[$ministry] = [];
            foreach ($people as $person) {
                $id = is_array($person) ? $person['id'] : $person;
                $participants[$ministry][$id] = ['category' => $ministry];
            }
        }

        $service->participants()->sync([]);
        if (count($participants)) {
            foreach ($participants as $category => $participant) {
                $service->participants()->attach($participant);
            }
        }

        if ($service->isChanged()) {
            $service->storeDiff();
            event(new ServiceUpdated($service, $originalParticipants));
        }

        return response()->json($service->slug);
    }
}
