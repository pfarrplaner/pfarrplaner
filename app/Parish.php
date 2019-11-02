<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Parish extends Model
{
    protected $fillable = [
        'name',
        'code',
        'city_id',
        'address',
        'zip',
        'city',
        'phone',
        'email'
    ];

    public function owningCity()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function streetRanges()
    {
        return $this->hasMany(StreetRange::class);
    }

    public function importStreetsFromCSV($csv)
    {
        /** @var StreetRange $streetRange */
        foreach ($this->streetRanges as $streetRange) {
            $streetRange->delete();
        }

        $ctr = 0;

        $records = [];
        foreach (explode("\r\n", $csv) as $line) {
            $records[] = str_getcsv($line, ';');
        }
        unset ($records[0]);
        array_walk($records, function (&$record) use (&$ctr) {
            if ($record[4] == $this->code) {
                $record[5] = explode(' bis ', $record[5]);
                $record[6] = explode(' bis ', $record[6]);
                $streetRange = new StreetRange([
                    'parish_id' => $this->id,
                    'name' => $record[0],
                    'odd_start' => $record[5][0],
                    'odd_end' => $record[5][1],
                    'even_start' => $record[6][0],
                    'even_end' => $record[6][1],
                ]);
                $streetRange->save();
                $ctr++;
            }
        });
        return $ctr;
    }

    public function scopeByAddress(Builder $query, $street, $number)
    {
        return $this->whereHas('streetRanges', function ($query2) use ($street, $number) {
            $query2->where('name', $street);
            if (($number % 2) == 0) {
                $query2->where('even_start', '<=', $number)
                    ->where('even_end', '>=', $number);
            } else {
                $query2->where('odd_start', '<=', $number)
                    ->where('odd_end', '>=', $number);
            }
        });
    }
}
