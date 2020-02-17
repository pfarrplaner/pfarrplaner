<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Baptism;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MinistryHomeScreen extends AbstractHomeScreen
{
    protected $hasConfiguration = true;

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();

        $start = Carbon::now();
        $end = Carbon::now()->addMonth(2);

        $services = Service::with(['baptisms', 'weddings', 'funerals', 'location', 'day'])
            ->select(['services.*', 'days.date'])
            ->join('days', 'days.id', '=', 'day_id')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->whereHas('day', function ($query) use ($start, $end) {
                $query->where('date', '>=', $start)
                    ->where('date', '<=', $end);
            })
            ->orderBy('days.date', 'ASC')
            ->orderBy('time', 'ASC')
            ->get();

        $missing = Service::withOpenMinistries(unserialize($user->getSetting('homeScreen_ministries', '')) ?: []);

        return $this->renderView('homescreen.ministry', compact('user', 'services', 'missing'));
    }

    public function renderConfigurationView()
    {
        return view('homescreen.ministry.config')->render();
    }


    public function setConfiguration(Request $request)
    {
        parent::setConfiguration($request);
        $data = $request->get('homeScreen');
        Auth::user()->setSetting('homeScreen_ministries', serialize($data['ministries']));
    }


}
