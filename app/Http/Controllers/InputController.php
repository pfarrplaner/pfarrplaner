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

use App\Inputs\AbstractInput;
use App\Inputs\Inputs;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;

/**
 * Class InputController
 * @package App\Http\Controllers
 */
class InputController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        $inputs = Inputs::all();
        return Inertia::render('Inputs/Index', compact('inputs'));
    }

    /**
     * @param Request $request
     * @param $input
     * @return Application|Factory|View
     */
    public function setup(Request $request, $input)
    {
        $input = $this->getInputClass($input);
        return $input->setup($request);
    }

    /**
     * @param $input
     * @return AbstractInput
     */
    protected function getInputClass($input): AbstractInput
    {
        $inputClass = 'App\\Inputs\\' . ucfirst($input) . 'Input';
        if (class_exists($inputClass)) {
            $input = new $inputClass();
            if (!$input->canEdit()) {
                return redirect()->back();
            }
            return $input;
        } else {
            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @param $input
     */
    public function input(Request $request, $input)
    {
        $input = $this->getInputClass($input);
        return $input->input($request);
    }

    /**
     * @param Request $request
     * @param $input
     */
    public function save(Request $request, $input)
    {
        $input = $this->getInputClass($input);
        return $input->save($request);
    }

}
