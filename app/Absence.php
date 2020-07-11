<?php

namespace App;

use App\Tools\StringTool;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $fillable = [
        'from',
        'to',
        'user_id',
        'reason',
        'replacement_notes',
    ];

    protected $dates = [
        'from', 'to'
    ];

    public function replacements() {
        return $this->hasMany(Replacement::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function approvals() {
        return $this->hasMany(Approval::class);
    }

    public function replacementText($prefix = '') {
        $prefix = $prefix ? $prefix.' ' : '';
        $replacements = $this->replacements;
        if (count($replacements) == 1) {
            return $prefix.$replacements->first()->toText();
        } else {
            $r = [];
            /** @var Replacement $replacement */
            foreach ($replacements as $replacement) {
                $r[] = $replacement->toText(true);
            }
            return $prefix.join('; ', $r);
        }
    }

    public function durationText() {
        return StringTool::durationText($this->from, $this->to);
    }

    public function getReplacingUserIds(): Collection {
        $ids = [];
        foreach ($this->replacements as $replacement) {
            foreach ($replacement->users as $user) {
                if (is_object($user)) $ids[] = $user->id;
            }
        }
        return (new Collection(array_unique($ids)));
    }

    public function fullDescription() {
        $t = $this->replacementText();
        if ($t) $t = ' [V: '.$t.']';
        return $this->user->fullName().' ('.$this->reason.')'.$t;
    }

    /**
     * Calculate, how many days of this absence fall into a certain period
     * This is used for the colspans in calendar view
     * @param Carbon $start Start date
     * @param Carbon $end End date
     */
    public function showableDays($start, $end) {
        $myFrom = max($start, $this->from);
        $myTo = min($end, $this->to);
        return $myTo->diffInDays($myFrom)+1;
    }

    public function delete()
    {
        foreach ($this->replacements as $replacement) $replacement->delete();
        return parent::delete();
    }


    public function scopeVisibleForUser($query, $user)
    {
        $userId = $user->id;
        $users = $user->getViewableAbsenceUsers();

        return $query->whereIn('user_id', $users->pluck('id'))
            ->orWhere(function ($query2)  use ($userId) {
                $query2->whereHas('replacements', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            });
    }


    public function scopeByUserAndPeriod($query, User $user, Carbon $start, Carbon $end) {
        return $query->where('user_id', $user->id)
            ->where('from', '<=', $end)
            ->where('to', '>=', $start);
    }
}
