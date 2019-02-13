<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use \Venturecraft\Revisionable\RevisionableTrait;

    protected $revisionEnabled = true;
    protected $revisionFormattedFieldNames = array(
        'day_id' => 'Tag',
        'location_id' => 'Ort',
        'time' => 'Uhrzeit',
        'pastor' => 'Pfarrer',
        'organist' => 'Organist',
        'sacristan' => 'Mesner',
        'description' => 'Besonderheiten',
        'city_id' => 'Kirchengemeinde',
        'special_location' => 'Ort (Freitext)',
        'need_predicant' => 'Prädikant benötigt',
    );

    protected $fillable = ['day_id', 'location_id', 'time', 'pastor', 'organist', 'sacristan', 'description', 'city_id', 'special_location', 'need_predicant'];

    public static function boot()
    {
        parent::boot();
    }

    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function locationText() {
        return $this->special_location ?: $this->location->name;
    }

    public function notifyOfCreation(User $author, $text) {

        $mailText = sprintf($text, $author->name.' ('.$author->email.')')."\r\n\r\n"
            ."Gottesdienst:\r\n=============\r\n";

        foreach ($this->revisionFormattedFieldNames as $key => $name) {
            $attribute = $this->getAttribute($key);
            if ($key == 'time') $attribute = strftime('%H:%M', strtotime($attribute));
            if ($key == 'day_id') {
                if ($this->special_location) {
                    $attribute = $this->special_location;
                } else {
                    $attribute = strftime('%A, %d. %B %Y', $this->day->date->getTimestamp());
                }
            }
            if ($key == 'city_id') $attribute = $this->city->name;
            if ($key == 'location_id') $attribute = $this->location->name;
            if ($key != 'special_location') $mailText .= 'Feld "'.$name.'": "'.$attribute.'"'."\r\n";
        }

        $mailText .= "\r\n\r\nDiese Benachrichtigung wurde automatisch erzeugt.";

        $this->notify($mailText);
    }

    public function notifyOfChanges(User $author, $text) {

        $mailText = sprintf($text, $author->name.' ('.$author->email.')')."\r\n\r\n"
        ."Gottesdienst:\r\n=============\r\n"
        .$this->day->date->format('d.m.Y')
            .', '.strftime('%H:%M Uhr', strtotime($this->time))
            .', '.($this->special_location ?: $this->location->name)."\r\n\r\n"
        ."Änderungen:\r\n===========\r\n";

        foreach ($this->revisionFormattedFieldNames as $key => $name) {
            $attribute = $this->getAttribute($key);
            $original = $this->getOriginal($key);
            if ($key == 'time') {
                $attribute = strftime('%H:%M', strtotime($attribute));
                $original = strftime('%H:%M', strtotime($original));
            }
            if ($key == 'location_id') {
                if ($this->special_location) {
                    $original = $this->getOriginal('special_location');
                    $attribute = $this->getAttribute('special_location');
                } else {
                    $original = Location::find($original)->name;
                    $attribute = $this->location->name;
                }
            }
            if ($key == 'city_id') {
                $attribute = $this->city->name;
                $original = City::find($original)->name;
            }
            if ($key == 'day_id') {
                $attribute = $this->day->date->format('d.m.Y');
                $original = Day::find($original)->date->format('d.m.Y');
            }
            if ($key != 'special_location') {
                if ($attribute != $original) {
                    $mailText .= 'Feld "'.$name.'": "'.$attribute.'" (vorher: "'.$original.'"'.")\r\n";
                }
            }
        }

        $mailText .= "\r\n\r\nDiese Benachrichtigung wurde automatisch erzeugt.";

        $this->notify($mailText);
    }

    protected function notify ($text) {
        $users = User::whereHas('cities', function($query){
            $query->where('city_id', $this->city_id);
        })->where('notifications', 1)
            ->get();

        foreach ($users as $user) {
            mail($user->email, 'Gottesdienst am '        .$this->day->date->format('d.m.Y')
                .', '.strftime('%H:%M Uhr', strtotime($this->time))
                .', '.($this->special_location ?: $this->location->name)."\r\n\r\n",
                utf8_decode($text), 'From: no-reply@tailfingen.de');
        }
    }

}
