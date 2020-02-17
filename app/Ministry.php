<?php


namespace App;


class Ministry
{
    public static function all() {
        return Participant::all()
            ->pluck('category')
            ->unique()
            ->reject(
                function ($value, $key) {
                    return in_array($value, ['P', 'O', 'M', 'A']);
                }
            );
    }

    public static function title($title) {
        if ($title == 'P') return 'Pfarrer*in';
        if ($title == 'O') return 'Organist*in';
        if ($title == 'M') return 'Mesner*in';
        if ($title == 'A') return 'Weitere Beteiligte';
        return $title;
    }
}
