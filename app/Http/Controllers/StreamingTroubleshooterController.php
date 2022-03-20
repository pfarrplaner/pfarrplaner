<?php

namespace App\Http\Controllers;

use App\Broadcast;
use App\City;
use App\Helpers\YoutubeHelper;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class StreamingTroubleshooterController extends Controller
{

    public function index(Request $request, $city)
    {
        $city = City::where('name', $city)->first();
        if(!$city) abort(404);

        if (! $request->hasValidSignature()) {
            abort(401);
        }

        $activeBroadcasts = Broadcast::getActive($city);
        $inactiveBroadcasts = Broadcast::getInactive($city);

        $nextServicesWithStream = Service::inCity($city)
            ->startingFrom(Carbon::now()->setTime(0,0,0))
            ->where('youtube_url', '!=', '')
            ->ordered()
            ->get();

        $nextServicesWithoutStream = Service::inCity($city)
            ->startingFrom(Carbon::now()->setTime(0,0,0))
            ->where(function ($query) {
                $query->where('youtube_url', '')
                    ->orWhereNull('youtube_url');
            })
            ->ordered()
            ->limit(10)
            ->get();



        return Inertia::render('streaming/troubleshooter', compact('city', 'activeBroadcasts', 'inactiveBroadcasts', 'nextServicesWithStream', 'nextServicesWithoutStream'));
    }

    public function activateService(Service $service)
    {
        $broadcast = Broadcast::get($service);
        if (null === $broadcast) {
            $broadcast = Broadcast::create($service);
            $service->update(['youtube_url' => $broadcast->getSharerUrl()]);
        }
        $broadcast->activate();
        return redirect(URL::signedRoute('streaming.troubleshooter', strtolower($service->city->name)));
    }

    public function activateBroadcast($city, $broadcast)
    {
        $city = City::where('name', $city)->first();
        if(!$city) abort(404);

        $broadcast = Broadcast::getFromId($broadcast, $city);
        $broadcast->activate();
        return redirect(URL::signedRoute('streaming.troubleshooter', strtolower($city->name)));
    }

    public function resetService(Service $service)
    {
        $youtube = YoutubeIntegration::get($service->city)->getYoutube();
        $youtube->liveBroadcasts->delete(YoutubeHelper::getCode($service->youtube_url));
        $broadcast = Broadcast::create($service);
        $service->update(['youtube_url' => $broadcast->getSharerUrl()]);
        return redirect(URL::signedRoute('streaming.troubleshooter', strtolower($service->city->name)));
    }
}
