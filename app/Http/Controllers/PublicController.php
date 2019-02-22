<?php

namespace App\Http\Controllers;

use App\City;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected $vacationData = [];

    protected function findUserByLastName($lastName) {
        return User::with('cities')->where('name', 'like', '%'.$lastName)->first();
    }

    protected function getVacationers($start, $end)
    {
        $vacationers = [];
        if (env('VACATION_URL')) {
            if (!count($this->vacationData)) {
                $this->vacationData = json_decode(file_get_contents(env('VACATION_URL')), true);
            }
            foreach ($this->vacationData as $key => $datum) {
                $vacStart = Carbon::createFromTimeString($datum['start']);
                $vacEnd = Carbon::createFromTimeString($datum['end']);
                if ((($vacStart < $start) && ($vacEnd >= $start)) || (($vacStart >= $start) && ($vacStart <= $end))) {
                    if (preg_match('/(?:U:|FB:) (\w*)/', $datum['title'], $tmp)) {
                        preg_match('/V: ((?:\w|\/)*)/', $datum['title'], $tmp2);
                        $sub = [];
                        foreach (explode('/', $tmp2[1]) as $name) {
                            $sub[] = $this->findUserByLastName(trim($name));
                        }

                        $vacationers[] = [
                            'away' => $this->findUserByLastName($tmp[1]),
                            'substitute' => $sub,
                            'start' => $vacStart,
                            'end' => $vacEnd,
                        ];
                    }
                }
            }
        }
        return $vacationers;
    }

    public function absences() {

        $start = Carbon::now()
            ->setTime(0,0,0);
        $end = Carbon::createFromDate($start->year, $start->month, 1)
            ->setTime(0,0,0)
            ->addMonth(2)
            ->subSecond(1);

        $vacations = $this->getVacationers($start, $end);
        $cities = City::orderBy('name', 'ASC')->get();

        return view('public.absences', [
            'vacations' => $vacations,
            'start' => $start,
            'end' => $end,
            'cities' => $cities,
        ]);
    }
}
