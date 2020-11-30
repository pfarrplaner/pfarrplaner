<?php

namespace App\Http\Controllers;

use App\CalendarConnection;
use Illuminate\Http\Request;

class CalendarConnectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendarconnection.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CalendarConnection  $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function show(CalendarConnection $calendarConnection)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CalendarConnection  $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function edit(CalendarConnection $calendarConnection)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CalendarConnection  $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CalendarConnection $calendarConnection)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CalendarConnection  $calendarConnection
     * @return \Illuminate\Http\Response
     */
    public function destroy(CalendarConnection $calendarConnection)
    {
        //
    }
}
