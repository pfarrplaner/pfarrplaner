<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceGroup extends Model
{
    protected $fillable = ['name'];

    public function services() {
        return $this->belongsToMany(Service::class);
    }

    public static function createIfMissing($list) {
        $result = [];
        foreach ($list as $element) {
            if (is_numeric($element)) {
                $result[] = $element;
            } else {
                $sg = new ServiceGroup(['name' => $element]);
                $sg->save();
                $result[] = $sg->id;
            }
        }
        return $result;
    }
}
