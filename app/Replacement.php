<?php

namespace App;

use App\Tools\StringTool;
use Illuminate\Database\Eloquent\Model;

class Replacement extends Model
{
    protected $fillable = ['absence_id', 'from', 'to'];
    protected $dates = ['from', 'to'];

    public function absence() {
        return $this->belongsTo(Absence::class);
    }

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function toText(bool $withDates = false) {
        $u = [];
        /** @var User $user */
        foreach ($this->users as $user) {
            $u[] = $user->lastName();
        }
        return join(' | ', $u).($withDates ? ' ('.StringTool::durationText($this->from, $this->to).')' : '');
    }
}
