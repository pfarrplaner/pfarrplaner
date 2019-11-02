<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 06.08.2019
 * Time: 09:01
 */

namespace App\Reports;


use App\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmbedServiceTableReport extends AbstractEmbedReport
{

    public $title = 'Liste von Gottesdiensten';
    public $group = 'Website (Gemeindebaukasten)';
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Gottesdiensttabelle in die Website der Gemeinde';
    public $icon = 'fa fa-file-code';

    public function setup()
    {
        $cities = Auth::user()->cities;
        $locations = Location::whereIn('city_id', $cities->pluck('id'))->get();
        return $this->renderSetupView(compact('cities', 'locations'));
    }

    public function render(Request $request)
    {
        $request->validate([
            'listType' => 'required',
            'ids' => 'required',
            'cors-origin' => 'required|url',
        ]);

        $listType = $request->get('listType');
        $ids = join(',', $request->get('ids'));

        $limit = $request->get('limit') ?: 5;
        $corsOrigin = $request->get('cors-origin');

        $url = route('embed.'.$listType, compact('ids', 'limit'));
        if ($corsOrigin) $url.='?cors-origin='.urlencode($corsOrigin);

        $randomId = uniqid();

        return view('reports.render.embedservicetable', compact('url', 'randomId'));
    }


}