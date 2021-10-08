<?php

namespace App\Http\Controllers;

use App\City;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $writableCityIds = Auth::user()->writableCities->pluck('id');
        $teams = Team::with('city', 'users')
            ->whereIn('city_id', Auth::user()->cities->pluck('id'))
            ->orderBy('name')
            ->get()
            ->map(function ($item) use ($writableCityIds) {
                $item->writable = $writableCityIds->contains($item->city_id);
                return $item;
            });
        return Inertia::render('Teams/Index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $counter = Team::whereIn('city_id', Auth::user()->cities->pluck('id'))
                ->orderBy('name')
                ->count() + 1;
        $team = Team::create([
                                 'name' => 'Neues Team #' . $counter,
                                 'city_id' => Auth::user()->cities->pluck('id')->first(),
                             ]);
        return redirect()->route('team.edit', $team->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Team $team
     * @return \Inertia\Response
     */
    public function edit(Team $team)
    {
        $team->load(['city', 'users']);
        $cities = Auth::user()->writableCities;
        $users = User::all();
        return Inertia::render('Teams/TeamEditor', compact('team', 'cities', 'users'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Team $team;
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Team $team)
    {
        $data = $this->validateRequest($request);
        $team->update($data);
        if (isset($data['users'])) $team->users()->sync(collect($data['users'])->pluck('id'));
        return redirect()->route('teams.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Team $team
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index');
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'name' => 'required|string',
            'city_id' => 'required|int|exists:cities,id',
            'users' => 'nullable',
                                  ]);
    }

    /**
     * @param City $city
     * @return \Illuminate\Http\JsonResponse
     */
    public function byCity(City $city)
    {
        $teams = Team::with('users')->where('city_id', $city->id)->orderBy('name')->get();
        return response()->json($teams);
    }
}
