<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 13:45
 */

namespace App\CalendarLinks;


use App\Service;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllServicesCalendarLink extends AbstractCalendarLink
{

    protected $title = 'Gottesdienste nach Kirchengemeinden';
    protected $description = 'Kalender, der alle Gottesdienste ausgewählter Kirchengemeinden enthält';

    public function setupData() {
        $cities = Auth::user()->cities;
        return compact('cities');
    }

    public function setDataFromRequest(Request $request)
    {
        $this->data['cities'] = join('-', $request->get('includeCities') ?: []);
    }

    public function getRenderData(Request $request, User $user)
    {
        $cityIds = explode('-', $request->get('cities', ''));
        return Service::with(['day', 'location'])
            ->whereIn('city_id', $cityIds)
            ->get();

    }


}