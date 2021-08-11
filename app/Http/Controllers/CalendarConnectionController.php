<?php

namespace App\Http\Controllers;

use App\CalendarConnection;
use App\Calendars\Exchange\ExchangeCalendar;
use App\Jobs\SyncEntireCalendarConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CalendarConnectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function exchangeCalendars(Request $request)
    {
        $data = $request->validate(['credentials1' => 'required|string', 'credentials2' => 'required|string']);

        $exchange = new ExchangeCalendar('mail.elkw.de', $data['credentials1'], $data['credentials2'], '2010_SP2', '');
        $folders = $exchange->getAllCalendars();
        return response()->json($folders);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $calendarConnections = CalendarConnection::where('user_id', Auth::user()->id)->get();
        return Inertia::render('CalendarConnections/Index', compact('calendarConnections'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $calendarConnection = CalendarConnection::create(
            [
                'user_id' => Auth::user()->id,
                'title' => 'Neue Kalenderverbindung',
                'credentials1' => Auth::user()->email ?? '',
                'credentials2' => '',
                'connection' => '',
                'connection_type' => CalendarConnection::CONNECTION_TYPE_OWN,
            ]
        );
        $calendarConnection->update(['title' => 'Neue Kalenderverbindung #' . $calendarConnection->id]);
        return redirect()->route('calendarConnection.edit', $calendarConnection->id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\CalendarConnection $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function show(CalendarConnection $calendarConnection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\CalendarConnection $calendarConnection
     * @return \Inertia\Response
     */
    public function edit(CalendarConnection $calendarConnection)
    {
        $cities = Auth::user()->cities;
        return Inertia::render('CalendarConnections/Setup', compact('calendarConnection', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\CalendarConnection $calendarConnection
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CalendarConnection $calendarConnection)
    {
        $data = $this->validateRequest($request);
        $calendarConnection->update($data);
        $calendarConnection->cities()->sync($request->get('cities') ?? []);

        // dispatch full sync job
        SyncEntireCalendarConnection::dispatch($calendarConnection);

        return redirect()->route('calendarConnection.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\CalendarConnection $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalendarConnection $calendarConnection)
    {
        $calendarConnection->delete();
        return redirect()->route('calendarConnection.index');
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate(
            [
                'user_id' => 'required|int|exists:users,id',
                'title' => 'required|string',
                'credentials1' => 'required|string',
                'credentials2' => 'required|string',
                'connection_string' => 'required|string',
                'include_hidden' => 'nullable|int',
                'include_alternate' => 'nullable|int',
            ]
        );
    }
}
