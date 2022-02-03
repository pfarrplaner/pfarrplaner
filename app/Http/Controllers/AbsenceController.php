<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

use App\Absence;
use App\Approval;
use App\Events\AbsenceApproved;
use App\Events\AbsenceDemanded;
use App\Events\AbsenceRejected;
use App\Http\Requests\AbsenceRequest;
use App\Mail\Absence\AbsenceChecked;
use App\Mail\Absence\AbsenceRequested;
use App\Replacement;
use App\Service;
use App\Services\CalendarService;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

/**
 * Class AbsenceController
 * @package App\Http\Controllers
 */
class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the absence planner
     *
     * @return Response
     */
    public function index(Request $request, $year = 0, $month = 0)
    {
        if (false !== ($r = $this->redirectIfMissingParameters($request, 'absences.index', $year, $month))) {
            return $r;
        }

        $start = CalendarService::getStartOfPeriod($year, $month);
        $days = Absence::getDaysForPlanner($start->copy());
        $years = Absence::select(DB::raw('YEAR(absences.from) as year'))->distinct()->get()->pluck('year')->sort();

        return Inertia::render('Absences/Planner', compact('start', 'days', 'year', 'month', 'years'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create($year, $month, User $user, $day = 1)
    {
        $users = User::all();
        $workflowStatus = 0;
        if (Auth::user()->id == $user->id) {
            if (Auth::user()->can('selfAdminister', Absence::class)) $workflowStatus = 10;
        }

        $absence = Absence::create(
            [
                'user_id' => $user->id,
                'reason' => 'Urlaub',
                'from' => Carbon::create($year, $month, $day, 0, 0, 0),
                'to' => Carbon::create($year, $month, $day, 0, 0, 0),
                'workflow_status' => $workflowStatus,
            ]
        );
        return redirect()->route('absences.edit', $absence->id);
    }



    /**
     * Get user data for absence planner (api)
     * @return \Illuminate\Http\JsonResponse
     */
    public function users()
    {
        $users = Auth::user()->getViewableAbsenceUsers();
        foreach ($users as $key => $user) {
            $user->canEdit = false;
            if (($user->id == Auth::user()->id)
                || (Auth::user()->hasPermissionTo('fremden-urlaub-bearbeiten')
                    && (!$user->hasRole('Pfarrer*in'))
                    && (count(Auth::user()->writableCities->intersect($user->homeCities))))
            ) {
                $user->canEdit = true;
            }
        }
        return response()->json($users);
    }

    /**
     * Get displayable days for absence planner (api)
     * @param $start Start date
     * @param User $user User to be displayed
     * @return \Illuminate\Http\JsonResponse
     */
    public function days($start, User $user)
    {
        $start = CalendarService::getStartOfPeriod($start);
        $end = $start->copy()->addMonth(1)->subDay(1);
        $days = Absence::getDaysForPlanner($start->copy());

        $absences = Absence::where('user_id', $user->id)
            ->where('to', '>=', $start)
            ->where('from', '<=', $end)
            ->get();

        // Find out whether current user is a replacement for this absence
        if ($user->id != Auth::user()->id) {
            foreach ($absences as $absence) {
                $absence->replacing = false;
                $absence->canEdit = Auth::user()->can('update', $absence);
                /** @var Replacement $replacement */
                foreach ($absence->replacements as $replacement) {
                    if ($replacement->users->pluck('id')->contains(Auth::user()->id)) {
                        $absence->replacing = true;
                    }
                }
            }
        }

        foreach ($days as $index => $day) {
            $days[$index]['services'] = Service::atDate($days[$index]['date'])
                ->userParticipates($user)
                ->count();
            $days[$index]['busy'] = ($days[$index]['services'] > 0);
            $days[$index]['absent'] = false;
            $days[$index]['absence'] = null;
            $days[$index]['duration'] = 0;
            $days[$index]['show'] = true;
        }


        foreach ($absences as $absence) {
            $index = ($absence->from < $start ? 1 : $absence->from->day);
            $days[$index]['absence'] = $absence;
            $days[$index]['duration'] = $absence->to->diff($days[$index]['date'])->days + 1;
            $endIndex = ($absence->to > $end ? $end->day : $absence->to->day);
            for ($i = $index; $i <= $endIndex; $i++) {
                $days[$i]['absent'] = true;
                if ($i > $index) {
                    $days[$i]['show'] = false;
                }
            }
        }

        return response()->json($days);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Absence $absence
     * @return Response
     */
    public function edit(Request $request, Absence $absence)
    {
        $absence->load(['replacements', 'user', 'checkedBy', 'approvedBy']);
        $absence->user->load(['vacationAdmins', 'vacationApprovers']);

        $mayCheck = $absence->user->vacationAdmins->pluck('id')->contains(Auth::user()->id);
        $mayApprove = $absence->user->vacationApprovers->pluck('id')->contains(Auth::user()->id);
        $maySelfAdminister = Auth::user()->can('selfAdminister', $absence);


        $year = $month = null;
        if ($request->has('startMonth')) {
            list($month, $year) = explode('-', $request->get('startMonth'));
        }
        if (!$year) {
            $year = date('Y');
        }
        if (!$month) {
            $year = date('m');
        }
        $users = User::all();
        return Inertia::render(
            'Absences/AbsenceEditor',
            compact('absence', 'month', 'year', 'users', 'mayCheck', 'mayApprove', 'maySelfAdminister')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Absence $absence
     * @return Response
     */
    public function update(AbsenceRequest $request, Absence $absence)
    {
        $absence->update($request->validated());
        $absence->setupReplacements($request->get('replacements') ?: []);

        // check workflow status and send appropriate notifications
        switch ($absence->workflow_status) {
            case Absence::STATUS_NEW:
                Mail::to($absence->user->vacationAdmins)->send(new AbsenceRequested($absence));
                break;
            case Absence::STATUS_CHECKED:
                Mail::to($absence->user->vacationApprovers)->send(new AbsenceChecked($absence));
                break;
            case Absence::STATUS_APPROVED:
                Mail::to(collect([$absence->user])
                             ->merge($absence->user->vacationAdmins)
                             ->merge($absence->user->vacationApprovers))
                    ->send(new \App\Mail\Absence\AbsenceApproved($absence));
                break;
        }

        return redirect()->route(
            'absences.index',
            ['month' => $absence->from->format('m'), 'year' => $absence->from->format('Y')]
        );
    }

    /**
     * Remove the specified resource from storage. Send rejection notice if necessary.
     *
     * @param Absence $absence
     * @return Response
     */
    public function destroy(Request $request, Absence $absence)
    {
        if ($request->get('sendRejectionMail', false)) {
            $recipients = collect([$absence->user]);
            $recipients = $recipients->merge($absence->user->vacationAdmins);
            if ($absence->workflow_status >0) $recipients = $recipients->merge($absence->user->vacationApprovers);
            Mail::to($recipients)->send(new \App\Mail\Absence\AbsenceRejected($absence, Auth::user()));
        }

        $absence->delete();
        return redirect()->route(
            'absences.index',
            ['month' => $request->get('month'), 'year' => $request->get('year')]
        );
    }

    /**
     * @param Request $request
     * @param $route
     * @param $year
     * @param $month
     * @return bool|RedirectResponse
     */
    protected function redirectIfMissingParameters(Request $request, $route, $year, $month)
    {
        $defaultMonth = Carbon::now()->month;
        $defaultYear = Carbon::now()->year;

        $initialYear = $year;
        $initialMonth = $month;


        if ($month == 13) {
            $year++;
            $month = 1;
        }
        if (($year > 0) && ($month == 0)) {
            $year--;
            $month = 12;
        }

        if ((!$year) || (!$month) || (!is_numeric($month)) || (!is_numeric($year)) || (!checkdate($month, 1, $year))) {
            $year = $defaultYear;
            $month = $defaultMonth;
        }

        if (($year == $initialYear) && ($month == $initialMonth)) {
            return false;
        }

        $data = compact('month', 'year');
        $slave = $request->get('slave', 0);
        if ($slave) {
            $data = array_merge($data, compact('slave'));
        }

        return redirect()->route($route, $data);
    }




}
