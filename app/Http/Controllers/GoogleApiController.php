<?php

namespace App\Http\Controllers;

use App\Broadcast;
use App\City;
use App\Service;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class GoogleApiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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
            $data =                 [
                'google_auth_code' => $code,
                'google_access_token' => $token['access_token'],
            ];
            if (isset($token['refresh_token'])) $data['google_refresh_token'] = $token['refresh_token'];
            $city->update($data);
            return redirect($nextStep);
        }
    }

    function createBroadcast(Service $service) {
        $broadcast = Broadcast::create($service);
        $service->update(['youtube_url' => $broadcast->getSharerUrl()]);
        return response()->json(['url' => $broadcast->getSharerUrl(), 'liveDashboard' => $broadcast->getLiveDashboardUrl()]);
    }
}
