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

namespace App;

use App\Services\CalendarService;
use App\Tools\StringTool;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Absence
 * @package App
 */
class Absence extends Model
{
    public const STATUS_NEW = 0;
    public const STATUS_CHECKED = 1;
    public const STATUS_APPROVED = 2;
    public const STATUS_SELF_ADMINISTERED = 10;
    public const STATUS_SELF_ADMINISTERED_AND_APPROVED = 11;


    /**
     * @var string[]
     */
    protected $fillable = [
        'from',
        'to',
        'user_id',
        'reason',
        'replacement_notes',
        'workflow_status',
        'admin_notes',
        'approver_notes',
        'admin_id',
        'approver_id',
        'checked_at',
        'approved_at',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'from',
        'to',
        'checked_at',
        'approved_at',
    ];

    protected $appends = [
        'durationText', 'replacementText'
    ];

    protected $with = [
        'user', 'replacements'
    ];

    public function checkedBy()
    {
        return $this->hasOne(User::class, 'id', 'admin_id');
    }

    public function approvedBy()
    {
        return $this->hasOne(User::class, 'id', 'approver_id');
    }

// ACCESSORS
    public function getReplacementTextAttribute()
    {
        return $this->replacementText();
    }

    public function getDurationTextAttribute()
    {
        return $this->durationText();
    }

    /**
     * @return string
     */
    public function durationText()
    {
        return StringTool::durationText($this->from, $this->to);
    }
// END ACCESSORS

// SCOPES
    /**
     * @param $query
     * @param $user
     * @return mixed
     */
    public function scopeVisibleForUser($query, $user)
    {
        $userId = $user->id;
        $users = $user->getViewableAbsenceUsers();

        return $query->whereIn('user_id', $users->pluck('id'))
            ->orWhere(
                function ($query2) use ($userId) {
                    $query2->whereHas(
                        'replacements',
                        function ($query) use ($userId) {
                            $query->where('user_id', $userId);
                        }
                    );
                }
            );
    }

    public function scopeByPeriod(Builder $query, Carbon $start, Carbon $end)
    {
        return $query->where('from', '<=', $end)
            ->where('to', '>=', $start);
    }

    /**
     * @param $query
     * @param User $user
     * @param Carbon $start
     * @param Carbon $end
     * @return mixed
     */
    public function scopeByUserAndPeriod($query, User $user, Carbon $start, Carbon $end)
    {
        return $query->where('user_id', $user->id)
            ->where('from', '<=', $end)
            ->where('to', '>=', $start);
    }
// END SCOPES

// SETTERS
// SETTERS
    /**
     * Get all absences keyed by days
     * @param Collection|\Illuminate\Database\Eloquent\Collection $absences
     * @param Collection|\Illuminate\Database\Eloquent\Collection $days
     */
    public static function getByDays($absences, $days)
    {
        $dayAbsences = [];
        foreach ($days as $day) {
            $dayAbsences[$day->id] = collect();
            foreach ($absences as $absence) {
                if ($absence->containsDate($day->date)) {
                    $dayAbsences[$day->id]->push($absence);
                }
            }
        }
        return $dayAbsences;
    }

    /**
     * @return HasMany
     */
    public function replacements()
    {
        return $this->hasMany(Replacement::class);
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }

    /**
     * @return Collection
     */
    public function getReplacingUserIds(): Collection
    {
        $ids = [];
        foreach ($this->replacements as $replacement) {
            foreach ($replacement->users as $user) {
                if (is_object($user)) {
                    $ids[] = $user->id;
                }
            }
        }
        return (new Collection(array_unique($ids)));
    }

    /**
     * @return string
     */
    public function fullDescription()
    {
        $t = $this->replacementText();
        if ($t) {
            $t = ' [V: ' . $t . ']';
        }
        return $this->user->fullName() . ' (' . $this->reason . ')' . $t;
    }

    /**
     * @param string $prefix
     * @return string
     */
    public function replacementText($prefix = '')
    {
        $prefix = $prefix ? $prefix . ' ' : '';
        $r = [];
        /** @var Replacement $replacement */
        foreach ($this->replacements as $replacement) {
            $r[] = $replacement->toText();
        }
        return $prefix . join('; ', $r);
    }

    /**
     * Calculate, how many days of this absence fall into a certain period
     * This is used for the colspans in calendar view
     * @param Carbon $start Start date
     * @param Carbon $end End date
     */
    public function showableDays($start, $end)
    {
        $myFrom = max($start, $this->from);
        $myTo = min($end, $this->to);
        return $myTo->diffInDays($myFrom) + 1;
    }

    /**
     * @return bool|null
     * @throws Exception
     */
    public function delete()
    {
        foreach ($this->replacements as $replacement) {
            $replacement->delete();
        }
        return parent::delete();
    }

    public function containsDate(Carbon $date)
    {
        return ($date >= $this->from) && ($date <= $this->to);
    }


    /**
     * Get an array of displayable dates for the AbsencePlanner
     * @param Carbon $start Start of displayed month
     * @return array Day data
     */
    public static function getDaysForPlanner(Carbon $start)
    {
        $end = $start->copy()->addMonth(1)->subSecond(1);
        $holidays = CalendarService::getHolidays($start, $end);
        $days = [];
        while ($start <= $end) {
            $day = ['day' => $start->day, 'holiday' => false, 'date' => $start->copy()];
            foreach ($holidays as $holiday) {
                $day['holiday'] = $day['holiday'] || (($start >= $holiday['start']) && ($start <= $holiday['end']));
            }
            $days[$start->day] = $day;
            $start->addDay(1);
        }
        return $days;
    }

    /**
     * @param $data
     */
    public function setupReplacements($data)
    {
        $this->load('replacements');
        if (count($this->replacements)) {
            foreach ($this->replacements as $replacement) {
                $replacement->delete();
            }
        }
        foreach ($data as $replacementData) {
            $replacement = new Replacement(
                [
                    'absence_id' => $this->id,
                    'from' => max(Carbon::createFromFormat('d.m.Y', $replacementData['from']), $this->from),
                    'to' => min(Carbon::createFromFormat('d.m.Y', $replacementData['to']), $this->to),
                ]
            );
            $replacement->save();
            if (isset($replacementData['users'])) {
                foreach ($replacementData['users'] as $id => $userData) {
                    $replacementData['users'][$id] = $userData['id'] ?? $userData;
                }
                $replacement->users()->sync($replacementData['users']);
            }
            $replacementIds[] = $replacement->id;
        }
    }



}
