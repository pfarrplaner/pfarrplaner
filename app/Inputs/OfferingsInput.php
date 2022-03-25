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
use App\Location;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class OfferingsInput
 * @package App\Inputs
 */
class OfferingsInput extends AbstractInput
{

    /**
     * @var string
     */
    public $title = 'Opferplan';
    public $description = 'Opferzwecke für ein ganzes Jahr bearbeiten';
    /**
     * @var string
     */
    protected $setupView = 'inputs.offerings.setup';

    public function canEdit(): bool
    {
        return Auth::user()->can('gd-opfer-bearbeiten');
    }

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function setup(Request $request)
    {
        $cities = Auth::user()->cities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        return Inertia::render('Inputs/Offerings/Setup', compact('cities', 'locations'));
    }


    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function input(Request $request)
    {
        $setup = $request->validate(
            [
                'from' => 'required|date_format:d.m.Y',
                'to' => 'required|date_format:d.m.Y',
                'cities.*' => 'required|exists:cities,id',
                'locations.*' => 'nullable|exists:locations,id',
            ]
        );

        $locations = Location::whereIn('city_id', $setup['cities'])->get();

        $query = Service::with(['city'])
            ->select('services.slug')
            ->between(
                Carbon::createFromFormat('d.m.Y', $setup['from']),
                Carbon::createFromFormat('d.m.Y', $setup['to'])
            )
            ->whereIn('city_id', $setup['cities'])
            ->ordered();

        if (count($setup['locations'] ?? [])) {
            $query->whereIn('services.location_id', $setup['locations']);
        }

        $serviceSlugs = $query->get()->pluck('slug');
        return Inertia::render('Inputs/Offerings/Form', compact('serviceSlugs'));
    }

    /**
     * @param Request $request
     * @return JsonResponse|void
     */
    public function save(Request $request)
    {
        $data = $request->validate([
                                       'id' => 'required|exists:services,id',
                                       'offering_goal' => 'nullable|string',
                                       'offering_type' => 'nullable|string',
                                       'offering_description' => 'nullable|string',
                                       'offerings_counter1' => 'nullable|string',
                                       'offerings_counter2' => 'nullable|string',
                                       'offering_amount' => 'nullable|string',
                                   ]);
        $service = Service::find($data['id']);
        if (!$service) {
            abort(404);
        }
        unset($data['id']);
        $service->update($data);
        return response()->json($service->slug);
    }

}
