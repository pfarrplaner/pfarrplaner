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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

abstract class AbstractHomeScreen
{

    /** @var bool $hasConfiguration true, if this view can be configured in user profile settings */
    protected $hasConfiguration = false;


    public function __construct()
    {
    }

    /**
     * Render the homescreen
     * @return mixed
     */
    abstract public function render();


    /**
     * Called to render a specific blade view
     * @param $viewName Name of the view to render
     * @param $data Data passed to the view (absence data will be added)
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function renderView($viewName, $data) {
        if (Auth::user()->manage_absences) {
            $data = array_merge($data, $this->getAbsences());
        }
        return view($viewName, $data);
    }

    /**
     * Get absence data for view
     * @return array Absence data
     */
    protected function getAbsences() {
        $absences = Absence::where('user_id', Auth::user()->id)->where('to', '>=', now())->get();
        $replacements = Replacement::with('absence')
            ->whereHas('users', function($query) {
                $query->where('users.id', Auth::user()->id);
            })
            ->where('to', '>=', now())
            ->orderBy('from')
            ->orderBy('to')
            ->get();
        return compact('absences', 'replacements');
    }


    /**
     * Get a view for configuration setttings
     * @return string
     */
    public function renderConfigurationView() {
        return 'Für diesen Startbildschirm gibt es nichts zu konfigurieren.';
    }


    /**
     * Save submitted configuration
     * @param Request $request Request
     */
    public function setConfiguration (Request $request) {

    }

}
