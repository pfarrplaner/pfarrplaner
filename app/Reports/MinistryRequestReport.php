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

namespace App\Reports;


use App\Day;
use App\Location;
use App\Mail\MinistryRequest;
use App\Service;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class MinistryRequestReport extends AbstractReport
{

    /**
     * @var string
     */
    public $title = 'Dienstanfrage senden';
    /**
     * @var string
     */
    public $group = 'Dienstplanung';
    /**
     * @var string
     */
    public $description = 'Sendet eine Anfrage mit zu belegenden Terminen an alle Teilnehmer eines Dienstes';
    /**
     * @var string
     */
    public $icon = 'fa fa-envelope';


    /**
     * @return Application|Factory|View
     */
    public function setup()
    {
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first()->date;
        $locations = Location::whereIn('city_id', Auth::user()->writableCities)->get();
        return $this->renderView('setup', compact('maxDate', 'locations'));
    }

    /**
     * @param Request $request
     */
    public function configure(Request $request)
    {
        $data = $request->validate(
            [
                'locations' => 'required',
                'locations.*' => 'exists:locations,id',
                'ministry' => 'required',
                'start' => 'required|date_format:d.m.Y',
                'end' => 'required|date_format:d.m.Y',
            ]
        );

        $cities = Location::select('city_id')->whereIn('id', $data['locations'])->get()->pluck('city_id');

        $data['start'] = Carbon::createFromFormat('d.m.Y', $data['start']);
        $data['end'] = Carbon::createFromFormat('d.m.Y', $data['end']);

        $data['users'] = User::with('services')->select('id')->whereHas(
            'services',
            function ($query) use ($data, $cities) {
                $query->where('service_user.category', $data['ministry']);
                $query->whereIn('city_id', $cities);
            }
        )->get()->unique()->sortBy('name');


        $data['services'] = Service::select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereIn('location_id', $data['locations'])
            ->whereHas(
                'day',
                function ($query) use ($data) {
                    $query->where('date', '>=', $data['start'])
                        ->where('date', '<=', $data['end']);
                }
            )->orderBy('days.date')->orderBy('time')->get();

        return $this->renderView('configure', $data);
    }

    public function addresses(Request $request)
    {
        $data = $request->validate(
            [
                'locations' => 'required',
                'ministry' => 'required',
                'start' => 'required|date_format:d.m.Y',
                'end' => 'required|date_format:d.m.Y',
                'services' => 'required',
                'recipients' => 'required',
            ]
        );

        $recipients = [];
        $data['users'] = [];
        foreach ($data['recipients'] as $recipient) {
            if (!is_numeric($recipient)) {
                $tmp = explode(' ', $recipient);
                $lastName = $tmp[count($tmp) - 1];
                unset($tmp[count($tmp) - 1]);
                $firstName = join(' ', $tmp);
                $thisUser= User::create(
                    [
                        'name' => $recipient,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                    ]
                );
                $data['users'][] = $thisUser;
                $recipients[] = $thisUser->id;
            } else {
                $thisUser = User::find($recipient);
                if (null !== $thisUser) {
                    $recipients[] = $recipient;
                    $data['users'][] = $thisUser;
                }
            }
        }
        $data['recipients'] = $recipients;

        $services = [];
        foreach ($data['services'] as $key => $service) {
            if ($service) {
                $services[] = $key;
            }
        }
        $data['services'] = $services;

        //$data['users'] = User::whereIn('id', $data['recipients'])->get();

        return $this->renderView('addresses', $data);
    }


    public function send(Request $request)
    {
        $data = $request->validate(
            [
                'locations' => 'required',
                'ministry' => 'required',
                'start' => 'required|date_format:d.m.Y',
                'end' => 'required|date_format:d.m.Y',
                'services' => 'required',
                'recipients' => 'required|exists:users,id',
                'address' => 'required',
                'address.*' => 'nullable|email',
                'text' => 'nullable|string',
            ]
        );

        foreach ($data['address'] as $id => $address) {
            if (!$address) {
                unset($data['address'][$id]);
            } else {
                User::find($id)->update(['email' => $address]);
            }
        }

        $userIds = array_keys($data['address']);

        $data['services'] = Service::select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereIn('services.id', explode(',', $data['services']))
            ->orderBy('days.date')
            ->orderBy('time')
            ->get();

        foreach ($userIds as $userId) {
            $user = User::find($userId);
            $sender = Auth::user();
            Mail::to($user->email)->cc($sender)->send(
                new MinistryRequest($user, $sender, $data['ministry'], $data['services'], $data['text'])
            );
        }


        return redirect('home')->with('success', 'Deine Anfrage wurde gesendet.');
    }

}
