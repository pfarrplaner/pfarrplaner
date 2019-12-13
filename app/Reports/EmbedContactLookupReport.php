<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 02.11.2019
 * Time: 12:30
 */

namespace App\Reports;


use App\City;
use App\Imports\EventCalendarImport;
use App\Parish;
use App\Service;
use App\StreetRange;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class EmbedContactLookupReport extends AbstractEmbedReport
{

    public $title = 'Ansprechpartner finden';
    public $group = 'Website (Gemeindebaukasten)';
    public $description = 'Erzeugt HTML-Code für die Einbindung einer Ansprechpartner-Box in die Website der Gemeinde';
    public $icon = 'fa fa-file-code';

    public function setup()
    {
        $cities = Auth::user()->cities;
        return $this->renderSetupView(compact('cities'));
    }

    public function render(Request $request)
    {

        $request->validate([
            'cors-origin' => 'required|url',
            'city' => 'required',
        ]);
        $city = City::findOrFail($request->get('city'));
        $corsOrigin = $request->get('cors-origin');
        $report = $this->getKey();

        $url = route('report.embed', compact('report', 'city', 'corsOrigin'));
        $randomId = uniqid();

        return $this->renderView('render', compact('url', 'randomId'));
    }


    public function embed(Request $request)
    {
        $city = City::findOrFail($request->get('city'));

        $street = $request->get('street', '');
        $number = $request->get('number', '');

        $streetRanges = StreetRange::whereHas('parish', function ($query) use ($city) {
            $query->where('parishes.city_id', $city->id);
        })->get();

        $streets = [];

        /** @var StreetRange $streetRange */
        foreach ($streetRanges as $streetRange) {
            $streets[$streetRange->name] = $streetRange->name;
        }
        ksort($streets);

        $randomId = uniqid();
        $corsOrigin = $request->get('cors-origin');
        $report = $this->getKey();

        $parish = false;
        if ($request->has('parish')) {
            $parish = Parish::findOrFail($request->get('parish'));
        } elseif (($street != '') && ($number != '')) {
            $parish = Parish::byAddress($street, $number)->first();
        } elseif ($request->cookie('parish')) {
            $parish = Parish::findOrFail($request->cookie('parish'));
        }

        $url = route('report.embed', compact('report', 'city', 'corsOrigin'));
        $report = $this->getKey();

        /** @var Response $response */
        $response = response()
            ->view($this->getViewName('embed'),
                compact('city', 'street', 'number', 'streets', 'randomId', 'url', 'parish', 'report'));

        if (false !== $parish) {
            $response->withCookie('parish', $parish->id);
        }

        return $response;
    }
}