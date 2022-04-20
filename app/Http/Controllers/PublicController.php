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

namespace App\Http\Controllers;

use App\Baptism;
use App\City;
use App\Day;
use App\Funeral;
use App\Mail\ContactFormMessage;
use App\Mail\MinistryRequestFilled;
use App\Service;
use App\User;
use App\Wedding;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

/**
 * Class PublicController
 * @package App\Http\Controllers
 */
class PublicController extends Controller
{

    /**
     * @param $lastName
     * @return Builder|Model|object|null
     */
    protected function findUserByLastName($lastName)
    {
        return User::with('cities')->where('name', 'like', '%' . $lastName)->first();
    }

    /**
     * @param Request $request
     * @param $city
     * @return Factory|RedirectResponse|View
     */
    public function childrensChurch(Request $request, $city)
    {
        $city = City::where('name', 'like', '%' . $city . '%')->first();
        if (!$city) {
            return redirect()->route('home');
        }

        $minDate = $maxDate = Carbon::now()->subDays(1);
        $latest = Service::inCity($city)
            ->ordered('DESC')
            ->limit(1)
            ->first();
        if ($latest) $maxDate = $latest->date;

        $serviceList = Service::with(['location', 'day'])
            ->between($minDate, $maxDate)
            ->where('cc', 1)
            ->inCity($city)
            ->ordered()
            ->get();
        $count = count($serviceList);
        $serviceList = $serviceList->groupBy('key_date');

        if ($count) {
            $minDate = Service::with(['location', 'day'])
                ->between($minDate, $maxDate)
                ->where('cc', 1)
                ->inCity($city)
                ->ordered()
                ->first()
                ->date;
            $maxDate = Service::with(['location', 'day'])
                ->between($minDate, $maxDate)
                ->where('cc', 1)
                ->inCity($city)
                ->ordered('DESC')
                ->first()
                ->date;
        }

        if ($request->is('*/pdf')) {
            $pdf = Pdf::loadView(
                'reports.childrenschurch.render',
                [
                    'start' => $minDate,
                    'end' => $maxDate,
                    'city' => $city,
                    'services' => $serviceList,
                    'count' => $count,
                ]
            );
            $filename = $minDate->format('Ymd') . '-' . $maxDate->format(
                    'Ymd'
                ) . ' Kinderkirche ' . $city->name . '.pdf';
            return $pdf->stream($filename);
        }

        return view(
            'public.cc',
            [
                'start' => $minDate,
                'end' => $maxDate,
                'city' => $city,
                'services' => $serviceList,
                'count' => $count,
            ]
        );
    }

    /**
     * @param string|City $city
     */
    public function nextStream($city)
    {
        $cityIds = [];
        if (!is_a(City::class, $city)) {
            $cities = explode(',', $city);
            foreach ($cities as $cityName) {
                $city = City::where('name', 'like', '%' . trim($cityName) . '%')->first();
                if ($city) {
                    $cityIds[] = $city->id;
                }
            }
        } else {
            $cityIds = [$city->id];
        }
        if (!count($cityIds)) {
            abort(404);
        }

        $service = Service::whereIn('city_id', $cityIds)
            ->startingFrom(Carbon::now()->setTime(0,0,0))
            ->where('youtube_url', '!=', '')
            ->ordered()
            ->first();
        if (!$service) {
            // get the last available service with a stream
            $service = Service::whereIn('city_id', $cityIds)
                ->where('youtube_url', '!=', '')
                ->ordered('DESC')
                ->first();
            if (!$service) {
                abort(404);
            }
        }

        return redirect($service->youtube_url);
    }

    public function ministryRequest(Request $request, $ministry, User $user, $services, $sender = null)
    {
        if (is_numeric($user)) $user = User::findOrFail($user);
        if (!$request->hasValidSignature()) abort(401);
        $services = Service::whereIn('services.id', explode(',', $services))
            ->ordered()
            ->get();
        $report = 'ministryRequest';
        return view(
            'reports.ministryrequest.request',
            compact('ministry', 'user', 'services', 'report', 'sender')
        );
    }

    public function ministryRequestFilled(Request $request, $ministry, User $user, $sender = null)
    {
        if (is_numeric($user)) $user = User::findOrFail($user);
        $services = [];
        foreach($request->get('services', []) as $key => $service) {
            if ($service) $services[] = $key;
        };
        $services = Service::whereIn('id', $services)->get();
        foreach ($services as $service) {
            $service->participants()->attach([$user->id => ['category' => $ministry]]);
        }

        if (null !== $sender) {
            $sender = User::find($sender);
            if (null !== $sender) {
                Mail::to($sender->email)
                    ->send(new MinistryRequestFilled($user, $sender, $ministry, $services));
            }
        }

        return view('reports.ministryrequest.thanks', compact('user', 'ministry'));
    }

    public function ministryPlan($cityName, $ministry)
    {
        $city = City::where('name', 'like', $cityName)->first();
        if (!$city) {
            abort(404);
        }

        if (str_contains($ministry, ',')) {
            $ministries = collect(explode(',', $ministry));
        } else {
            $ministries = collect([ucfirst($ministry)]);
        }
        $ministries = $ministries->map(
            function ($item) {
                return ucfirst(trim($item));
            }
        );

        $services = Service::where('city_id', $city->id)
            ->whereHas(
                'participants',
                function ($q) use ($ministries) {
                    return $q->whereIn('category', $ministries);
                }
            )
            ->startingFrom(Carbon::now()->subDays(1))
            ->ordered()
            ->get();

        return view('public.ministries.plan', compact('city', 'ministries', 'services'));
    }

    /**
     * @param $type
     * @param $id
     * @return Baptism|Funeral|Wedding|null
     */
    protected function getRite($type, $id) {
        switch ($type) {
            case 'taufe':
                return Baptism::findOrFail($id);
            case 'beerdigung':
                return Funeral::findOrFail($id);
            case 'trauung':
                return Wedding::findOrFail($id);
        }
        return null;
    }

    /**
     * @param Request $request
     * @param $type
     * @param $id
     * @return Application|Factory|View
     */
    public function showDimissorial(Request $request, $type, $id) {
        if (!$request->hasValidSignature()) abort(401);
        if (!$rite = $this->getRite($type, $id)) abort(404);

        if ($type == 'trauung') {
            if (!$request->has('spouse')) abort(404);
            $spouse = $request->get('spouse');
            $method = 'spouse'.$spouse.'_needs_dimissorial';
            if (!$rite->$method) abort(403);
        } else {
            if (!$rite->needs_dimissorial) abort(403);
            $spouse = null;
        }

        $rite->load(['service']);
        $type = ucfirst($type);
        return view('public.dimissorial.show', compact('rite', 'type', 'id', 'spouse'));
    }

    public function grantDimissorial(Request $request, $type, $id)
    {
        if (!$rite = $this->getRite($type, $id)) abort(404);
        $rite->load(['service']);
        if ($type == 'trauung') {
            if (!$request->has('spouse')) abort(404);
            $spouse = $request->get('spouse');
            $method = 'spouse'.$spouse.'_needs_dimissorial';
            if (!$rite->$method) dd($method);
            $rite->update(['spouse'.$spouse.'_dimissorial_received' => Carbon::now()]);
        } else {
            if (!$rite->needs_dimissorial) abort(403);
            $rite->update(['dimissorial_received' => Carbon::now()]);
        }
        $type = ucfirst($type);
        return view('public.dimissorial.thanks', compact('rite', 'type'));
    }

    public function submitContactForm(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'subject' => 'required|string',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required',
                                   ]);


        $client = new Client();
        $response = $client->post('https://www.google.com/recaptcha/api/siteverify', [
            'query' => [
                'secret' => config('recaptcha.secret'),
                'response' => $data['g-recaptcha-response'],
            ],
        ]);
        if (!json_decode($response->getBody(), true)['success']) abort(403);

        Mail::to('christoph.fischer@elkw.de')->send(new ContactFormMessage($data));
        return response('OK');
    }
}
