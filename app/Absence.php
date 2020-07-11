<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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

use App\Tools\StringTool;
use Carbon\Carbon;
use Exception;
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
    /**
     * @var string[]
     */
    protected $fillable = [
        'from',
        'to',
        'user_id',
        'reason',
        'replacement_notes',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'from',
        'to'
    ];

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
     * @return string
     */
    public function durationText()
    {
        return StringTool::durationText($this->from, $this->to);
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
        $replacements = $this->replacements;
        if (count($replacements) == 1) {
            return $prefix . $replacements->first()->toText();
        } else {
            $r = [];
            /** @var Replacement $replacement */
            foreach ($replacements as $replacement) {
                $r[] = $replacement->toText(true);
            }
            return $prefix . join('; ', $r);
        }
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
}
