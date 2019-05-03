<?php

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Service;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class PublicController extends Controller
{
    protected $vacationData = [];

    protected function findUserByLastName($lastName)
    {
        return User::with('cities')->where('name', 'like', '%' . $lastName)->first();
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

    public function absences()
    {
        $start = Carbon::now()
            ->setTime(0, 0, 0);
        $end = Carbon::createFromDate($start->year, $start->month, 1)
            ->setTime(0, 0, 0)
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

    /**
     * @param Request $request
     * @param $city
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function childrensChurch(Request $request, $city)
    {
        $city = City::where('name', 'like', '%'.$city.'%')->first();
        if (!$city) {
            return redirect()->route('home');
        }

        $minDate = Carbon::now();
        $maxDate = Day::orderBy('date', 'DESC')->limit(1)->get()->first()->date;

        $days = Day::where('date', '>=', $minDate)
            ->where('date', '<=', $maxDate)
            ->orderBy('date', 'ASC')
            ->get();

        $serviceList = [];
        foreach ($days as $day) {
            $serviceList[$day->date->format('Y-m-d')] = Service::with(['location', 'day'])
                ->where('day_id', $day->id)
                ->where('cc', 1)
                ->where('city_id', $city->id)
                ->orderBy('time', 'ASC')
                ->get();
        }

        $dates = [];
        foreach ($serviceList as $day => $services) {
            foreach ($services as $service) {
                $dates[] = $service->day->date;
            }
        }

        if (count($dates)) {
            $minDate = min($dates);
            $maxDate = max($dates);
        }

        if ($request->is('*/pdf')) {
            $pdf = Pdf::loadView('reports.render.childrenschurch', [
                'start' => $minDate,
                'end' => $maxDate,
                'city' => $city,
                'services' => $serviceList,
                'count' => count($dates),
            ]);
            $filename = $minDate->format('Ymd').'-'.$maxDate->format('Ymd').' Kinderkirche '.$city->name.'.pdf';
            return $pdf->stream($filename);
        }

        return view('public.cc', [
            'start' => $minDate,
            'end' => $maxDate,
            'city' => $city,
            'services' => $serviceList,
            'count' => count($dates),
        ]);
    }
}
