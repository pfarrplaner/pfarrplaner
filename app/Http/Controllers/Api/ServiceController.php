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
 * Date: 17.08.2019
 * Time: 11:09
 */

namespace App\Http\Controllers\Api;


use App\City;
use App\Day;
use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Liturgy;
use App\Mail\ServiceUpdated;
use App\Service;
use App\ServiceGroup;
use App\Subscription;
use App\Traits\HandlesAttachmentsTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Class ServiceController
 * @package App\Http\Controllers\Api
 */
class ServiceController extends Controller
{

    use HandlesAttachmentsTrait;

    /**
     * @param Day $day
     * @param City $city
     * @return mixed
     */
    public function byDayAndCity(Day $day, City $city)
    {
        return Service::select('id')
            ->where('city_id', $city->id)
            ->where('day_id', '=', $day->id)
            ->orderBy('time')
            ->get()
            ->pluck('id');
    }

    /**
     * @param Service $service
     * @return JsonResponse
     */
    public function show(Service $service)
    {
        $service->load(
            ['location', 'city', 'participants', 'weddings', 'funerals', 'baptisms', 'day', 'tags', 'serviceGroups']
        );
        $service->liturgy = Liturgy::getDayInfo($service->day);
        if (isset($liturgy['title']) && ($service->day->name == '')) {
            $service->day->name = $service->liturgy['title'];
        }
        return response()->json($service);
    }


    /**
     * @param User $user
     * @return JsonResponse
     */
    public function byUser(User $user)
    {
        $services = Service::select('services.*')
            ->with('location', 'city', 'participants', 'funerals', 'baptisms', 'weddings')
            ->whereHas(
                'participants',
                function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            )->startingFrom(Carbon::now())
            ->ordered()
            ->get();


        foreach ($services as $service) {
            $service->liturgy = Liturgy::getDayInfo($service->day);
        }
        return response()->json(compact('services'))->header('Access-Control-Allow-Origin', '*');
    }

    /**
     * Update a service
     * @param ServiceRequest $request Request with validated data
     * @param Service $service Service model
     * @return JsonResponse Response with new service data
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $service->trackChanges();
        $originalParticipants = $service->participants;
        $service->update($request->validated());
        if ($request->has('participants')) {
            $service->associateParticipants($request, $service);
            $service->checkIfPredicantNeeded();
        }

        if ($request->has('tags')) {
            $service->tags()->sync($request->get('tags') ?: []);
        }

        if ($request->has('serviceGroups')) {
            $service->serviceGroups()->sync(ServiceGroup::createIfMissing($request->get('serviceGroups') ?: []));
        }
        $this->handleAttachments($request, $service);

        if ($service->isChanged()) {
            $service->storeDiff();
            event(new \App\Events\ServiceUpdated($service, $originalParticipants));
        }

        return response()->json(compact('service'));
    }


    /**
     * Assign users to a service
     * @param Request $request
     * @param Service $service
     */
    public function assign(Request $request, Service $service)
    {
        $data = $request->validate([
                                       'ministry' => 'required|string',
                                       'users.*' => 'required|int|exists:users,id',
                                       'exclusive' => 'required|checkbox',
                                       'no-toggle' => 'nullable|checkbox',
                                   ]);
        $participants = $service->getSyncableParticipantsArray();
        if (!isset($participants[$data['ministry']])) {
            $participants[$data['ministry']] = [];
        }
        $existing = $participants[$data['ministry']];
        if ($data['exclusive']) {
            $participants[$data['ministry']] = [];
        }
        foreach ($data['users'] as $userId) {
            if (!$data['no-toggle']) {
                if (isset($existing[$userId])) {
                    unset($participants[$data['ministry']][$userId]);
                } else {
                    $participants[$data['ministry']][$userId]['category'] = $data['ministry'];
                }
            } else {
                $participants[$data['ministry']][$userId]['category'] = $data['ministry'];
            }
        }
        $service->syncParticipantsFromArray($participants);
        $service->refresh();
        $service->load(['baptisms', 'funerals', 'weddings', 'participants']);
        return response()->json(compact('service'));
    }

}
