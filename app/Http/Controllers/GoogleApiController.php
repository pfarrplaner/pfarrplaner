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

namespace App\Http\Controllers;

use App\Broadcast;
use App\City;
use App\Service;
use Google_Client;
use Google_Exception;
use Google_Service_YouTube;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Session;


/**
 * Class GoogleApiController
 * @package App\Http\Controllers
 */
class GoogleApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     * @throws Google_Exception
     */
    public function auth(Request $request)
    {
        if ($request->has('state')) {
            $state = json_decode($request->get('state'), true);
            if (!isset($state['city'])) {
                abort(404);
            }
            $city = City::findOrFail($state['city']);
            $nextStep = $state['nextStep'];
        } elseif ($request->has('city')) {
            $city = City::findOrFail($request->get('city'));
            $nextStep = $request->get('nextStep', '');
        } else {
            abort(404);
        }

        $client = new Google_Client();
        $client->setAuthConfig(base_path('config/client_secret.json'));
        $client->addScope(Google_Service_YouTube::YOUTUBE);
        $client->setRedirectUri(route('google-auth'));
        $client->setAccessType('offline');
        $client->setState(json_encode(['city' => $city->id, 'nextStep' => $nextStep]));
        $client->setIncludeGrantedScopes(true);

        if (!$request->has('code')) {
            if (($city->google_access_token != '') && ($city->google_refresh_token != '')) {
                $client->setAccessToken($city->google_access_token);
                $client->refreshToken($city->google_refresh_token);
                $city->update(['google_access_token' => $client->getAccessToken()['access_token']]);
                return redirect($nextStep);
            }
            if (!$client->getAccessToken()) {
                $auth_url = $client->createAuthUrl();
                return redirect($auth_url);
            }
        } else {
            $code = $request->get('code');
            $client->fetchAccessTokenWithAuthCode($code);
            $token = $client->getAccessToken();
            Session::put('google_access_token', $token);
            $data = [
                'google_auth_code' => $code,
                'google_access_token' => $token['access_token'],
            ];
            if (isset($token['refresh_token'])) {
                $data['google_refresh_token'] = $token['refresh_token'];
            }
            $city->update($data);
            return redirect($nextStep);
        }
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return JsonResponse
     */
    function createBroadcast(Request $request, Service $service)
    {
        $broadcast = Broadcast::create($service);
        $service->update(['youtube_url' => $broadcast->getSharerUrl()]);
        if ($request->get('json', false)) {
            return response()->json(
                ['url' => $broadcast->getSharerUrl(), 'liveDashboard' => $broadcast->getLiveDashboardUrl()]
            );
        } else {
            return redirect()->back()->with('success', 'Der Livestream wurde angelegt.');
        }
    }

    function deleteBroadcast(Service $service) {
        Broadcast::delete($service);
        return redirect()->back();
    }

    function refreshBroadcast(Service $service) {
        $broadcast = Broadcast::get($service);
        $broadcast->update();
        return redirect()->back()->with('success', 'Die Beschreibung des Gottesdiensts auf YouTube wurde angepasst.');
    }
}
