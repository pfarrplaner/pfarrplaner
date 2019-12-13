<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 13:42
 */

namespace App\CalendarLinks;


use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalServicesCalendarLink extends AbstractCalendarLink
{
    protected $title = 'Eigene Gottesdienste';
    protected $description = 'Kalender, der nur deine eigenen Gottesdienste enthält';

    public function setupRoute()
    {
        return route('ical.link', ['key' => $this->getKey()]);
    }

    public function setDataFromRequest(Request $request)
    {
        if (null !== Auth::user()) {
            $this->data['user'] = Auth::user()->id;
        }
    }

    public function getRenderData(Request $request, User $user)
    {
        $data = Service::select('services.*')
            ->with('location', 'day', 'participants')
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();
        return $data;
    }


}