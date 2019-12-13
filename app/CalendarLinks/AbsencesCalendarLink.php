<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 29.10.2019
 * Time: 14:19
 */

namespace App\CalendarLinks;


use App\Absence;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsencesCalendarLink extends AbstractCalendarLink
{

    protected $title = 'Urlaubskalender';
    protected $description = 'Kalender mit allen Urlaubseinträgen, die du sehen kannst';
    protected $viewName = 'absences';

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
        $users = $user->getViewableAbsenceUsers();
        $userId = $user->id;
        $data = Absence::whereIn('user_id', $users->pluck('id'))
            ->orWhere(function ($query2) use ($userId) {
                $query2->whereHas('replacements', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })->get();

        return $data;
    }


}