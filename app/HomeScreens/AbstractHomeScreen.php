<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 24.04.2019
 * Time: 14:32
 */

namespace App\HomeScreens;


use App\Misc\VersionInfo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

abstract class AbstractHomeScreen
{
    public function __construct()
    {
        $user = Auth::user();
        $cutOff = ($user->new_features ?: Carbon::createFromDate(2019,05, 14)->setTime(0,0,0));
         // flash what's new messages
        $info = Session::get('info', []);
        foreach (VersionInfo::getMessages()->sortByDesc('date')->where('date', '>=', $cutOff) as $message) {
            $info[] = '<b><span class="fa fa-sun"></span> Neu ab '.$message['date']->format('d.m.Y').'</b>:<br />'.$message['text'];
        }
        Session::put('info', $info);
        $user->new_features = Carbon::now();
        $user->save();
    }

    abstract public function render();
}