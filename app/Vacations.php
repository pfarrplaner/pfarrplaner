<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 27.06.2019
 * Time: 12:29
 */

namespace App;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class Vacations
{

    protected static function getVacationDataFromCache() {
        if (!Cache::has('vacationData')) {
            $vacationData = json_decode(file_get_contents(env('VACATION_URL')), true);
            foreach ($vacationData as $key => $datum) {
                $vacationData[$key]['start'] = Carbon::createFromTimeString($datum['start']);
                $vacationData[$key]['end'] = Carbon::createFromTimeString($datum['end']);
            }
            Cache::put('vacationData', $vacationData, 180);
        } else {
            $vacationData = Cache::get('vacationData');
        }
        return $vacationData;
    }

    public static function getByDay($day)
    {
        $vacationers = [];
        $vacationData = self::getVacationDataFromCache();

        foreach ($vacationData as $key => $datum) {
            if (($day->date >= $datum['start']) && ($day->date <= $datum['end'])) {
                if (preg_match('/(?:U:|FB:) (\w*)/', $datum['title'], $tmp)) {
                    $vacationers[$tmp[1]] = $tmp[0];
                }
            }
        }

        return $vacationers;
    }


    protected static function findUserByLastName($lastName)
    {
        return User::with('cities')->where('name', 'like', '%' . $lastName)->first();
    }



    public static function getByPeriodAndUser($start, $end, User $user = null)
    {
        return Absence::with('replacements')->byUserAndPeriod($user, $start, $end);
    }

}