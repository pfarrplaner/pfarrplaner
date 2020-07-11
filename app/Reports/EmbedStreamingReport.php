<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 06.08.2019
 * Time: 09:01
 */

namespace App\Reports;


use App\Location;
use App\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmbedStreamingReport extends AbstractEmbedReport
{

    public $title = 'Streaming von Gottesdiensten';
    public $group = 'Website (Gemeindebaukasten)';
    public $description = 'Erzeugt HTML-Code für die Einbindung eines Streaming-Gottesdiensts in die Website der Gemeinde';
    public $icon = 'fa fa-file-code';

    public function setup()
    {
        $cities = Auth::user()->cities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        return $this->renderSetupView(compact('cities'));
    }

    public function render(Request $request)
    {
        $data = $request->validate([
            'city' => 'required',
            'cors-origin' => 'required|url',
        ]);
        $data['url'] = route('embed.report', ['report' => 'streaming', 'city' => $data['city']]);
        $data['randomId'] = uniqid();
        return view('reports.embedstreaming.render', $data);
    }

    public function embed(Request $request) {
        if (!$request->has('city')) abort(404);
        $nextService = Service::where('city_id', $request->get('city'))
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereHas('day', function ($query) {
                $query->where('date', '>=', Carbon::now()->format('Y-m-d'));
            })
            ->where('youtube_url', '!=', '')
            ->orderBy('days.date')
            ->orderBy('time')
            ->first();

        $lastServices = Service::where('city_id', $request->get('city'))
            ->select('services.*')
            ->join('days', 'days.id', 'services.day_id')
            ->whereHas('day', function ($query) {
                $query->where('date', '<=', Carbon::now()->format('Y-m-d'));
            })
            ->orderBy('days.date', 'desc')
            ->orderBy('time', 'desc')
            ->limit(100)
            ->get();

        return view('reports.embedstreaming.embed', compact('nextService', 'lastServices'));
    }


}
