<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace App\Http\Controllers;

use App\City;
use App\Day;
use App\Liturgy;
use App\Service;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Class DayController
 * @package App\Http\Controllers
 */
class DayController extends Controller
{

    /**
     * @var array
     */
    protected $dataCache = [];

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cities = Auth::user()->writableCities;
        return view('days.create', compact('cities'));
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest();
        //$day = Day::existsForDate(Carbon::createFromFormat('d.m.Y', $data['date'])->format('Y-m-d'));
        $day = Day::where('date', Carbon::createFromFormat('d.m.Y', $data['date'])->format('Y-m-d'))->first();
        if ($day) {
            $day->update($data);
        } else {
            if (($data['day_type'] == Day::DAY_TYPE_LIMITED) && (count($data['cities']) == 0)) {
                return redirect()->back()->withInput()->withErrors(
                    ['cities' => 'Mindestens eine Kirchengemeinde muss ausgewählt werden.']
                );
            }
            $day = Day::create($data);
        }
        $day->cities()->sync($data['cities']);
        if (($data['day_type'] == Day::DAY_TYPE_LIMITED) && (count($day->cities) == 0)) {
            $day->delete();
        }
        return redirect()->route('calendar', $day->date->format('Y-m'));
    }

    /**
     * Validate and format data
     * @return array Validated data
     */
    protected function validateRequest(): array
    {
        $data = request()->validate(
            [
                'date' => 'required|date|date_format:d.m.Y',
                'name' => 'nullable',
                'description' => 'nullable',
                'day_type' => 'nullable|int|in:0,1',
                'cities.*' => 'nullable|int|exists:cities,id',
            ]
        );
        $data['day_type'] = $data['day_type'] ?? Day::DAY_TYPE_DEFAULT;
        if ($data['day_type'] == Day::DAY_TYPE_LIMITED) {
            request()->validate(['cities.*' => 'int|exists:cities,id']);
        }

        $data['cities'] = $data['cities'] ?? [];
        $data['name'] = $data['name'] ?? Liturgy::getDayInfo($data['date'])['title'] ?? '';
        $data['description'] = $data['description'] ?? '';
        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param Day $day
     * @return Response
     */
    public function show(Day $day)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Day $day
     * @return Response
     */
    public function edit(Day $day)
    {
        $cities = City::all();
        return view('days.edit', compact('day', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Day $day
     * @return Response
     */
    public function update(Day $day)
    {
        $data = $this->validateRequest();


        $day->update($data);
        $day->cities()->sync($data['cities']);
        $date = $day->date;
        if (($data['day_type'] == Day::DAY_TYPE_LIMITED) && (count($day->cities) == 0)) {
            $day->delete();
        }

        return redirect()->route('calendar', $day->date->format('Y-m'))
            ->with('success', 'Die Änderungen wurden gespeichert.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Day $day
     * @return Response
     */
    public function destroy(Day $day)
    {
        $date = $day->date;

        // limited and visible for other churches?
        if ($day->day_type == Day::DAY_TYPE_LIMITED) {
            foreach (Auth::user()->cities as $city) {
                if ($day->cities->contains($city)) {
                    $day->cities()->detach($city->id);
                }
            }
            if (count($day->cities) == 0) {
                $day->delete();
            }
        } else {
            $day->delete();
        }


        return redirect()->route('calendar', $date->format('Y-m'))
            ->with('success', 'Der ' . $date->format('d.m.Y') . ' wurde aus der Liste entfernt');
    }

    /**
     * @param $year
     * @param $month
     * @return Application|Factory|RedirectResponse|View
     */
    public function add($year, $month)
    {
        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            return redirect()->route('calendar', ['year' => date('Y'), 'month' => date('m')]);
        }
        $start = Carbon::create($year, $month, 1);
        $end = $start->copy()->addMonth(1)->subSecond(1);

        $days = [];
        $existing = [];
        $existingCities = [];
        $date = $start->copy();
        while ($date <= $end) {
            if ($existingDay = Day::where('date', $date->format('Y-m-d'))->first()) {
                $existing[$existingDay->date->format('Y-m-d')] = $existingDay;
                if ($existingDay->day_type == Day::DAY_TYPE_DEFAULT) {
                    $existingCities[$existingDay->date->format('Y-m-d')] = Service::where(
                        'day_id',
                        $existingDay->id
                    )->get()->pluck('city_id')->unique();
                }
            } else {
                $days[] = $date->day;
            }
            $date->addDay(1);
        }

        $days = collect($days);
        $existing = collect($existing);

        $cities = Auth::user()->writableCities;
        return view(
            'days.create',
            compact('year', 'month', 'cities', 'days', 'existing', 'start', 'end', 'existingCities')
        );
    }
}
