<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 05.08.2019
 * Time: 17:57
 */

namespace App\Http\Controllers;


use App\Day;
use App\Service;
use App\User;
use App\Vacations;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmbedController extends Controller
{

    public function __construct()
    {
        $this->middleware('cors');
    }


    /**
     * Return a table of services for specific locations
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedByLocations(Request $request, $ids, $limit = 10) {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with('location')
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('location_id', $ids)
            ->whereHas('day', function($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();
        return view('embed.services.table', compact('services', 'ids', 'title'));
    }

    /**
     * Return a table of services for specific cities
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedByCities(Request $request, $ids, $limit = 10) {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with(['location', 'participants'])
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', $ids)
            ->whereHas('day', function($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->doesntHave('funerals')
            ->doesntHave('weddings')
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();
        return response()
            ->view('embed.services.table', compact('services', 'locationIds', 'title'));

    }

    /**
     * Return a table of childrens-church services for specific cities
     * @param Request $request Request
     * @param string $ids comma-separated location ids
     * @param int $limit Limit (optional)
     */
    public function embedCCByCities(Request $request, $ids, $limit = 5) {
        $ids = explode(',', $ids);
        $title = $request->has('title') ? $request->get('title') : '';
        $services = Service::with(['location', 'participants'])
            ->select('services.*')
            ->join('days', 'services.day_id', '=', 'days.id')
            ->whereIn('city_id', $ids)
            ->whereHas('day', function($query) {
                $query->where('date', '>=', Carbon::now());
            })
            ->doesntHave('funerals')
            ->doesntHave('weddings')
            ->where('cc', true)
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->limit($limit)
            ->get();
        return response()
            ->view('embed.services.ccTable', compact('services', 'locationIds', 'title'));

    }


    /**
     * Return a table of upcoming vacations and replacements for a specific user
     * @param Request $request
     * @param User $user
     * @param $userId
     */
    public function embedUserVacations (Request $request, User $user) {
        $start = Carbon::now();
        $end = (clone $start)->addWeek(2);
        $vacations = Vacations::getByPeriodAndUser($start, $end, $user);
        return response()
            ->view('embed.user.vacations', compact('vacations'));
    }


}