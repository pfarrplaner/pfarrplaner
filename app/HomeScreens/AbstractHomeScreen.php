<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Absence;
use App\Misc\VersionInfo;
use App\Replacement;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

abstract class AbstractHomeScreen
{
    public function __construct()
    {
    }

    abstract public function render();

    public function renderView($viewName, $data) {
        if (Auth::user()->manage_absences) {
            $data = array_merge($data, $this->getAbsences());
        }
        return view($viewName, $data);
    }

    protected function getAbsences() {
        $absences = Absence::where('user_id', Auth::user()->id)->get();
        $replacements = Replacement::with('absence')
            ->whereHas('users', function($query) {
                $query->where('users.id', Auth::user()->id);
            })
            ->orderBy('from')
            ->orderBy('to')
            ->get();
        return compact('absences', 'replacements');
    }
}