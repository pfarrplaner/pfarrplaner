<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 06.12.2019
 * Time: 15:04
 */

namespace App\UI;


use Illuminate\Support\Facades\Session;

class FlashMessages
{
    public static function count()
    {
        return count(self::all());
    }

    public static function all()
    {
        $msg = [];

        foreach (['success', 'error', 'warning', 'info'] as $class) {
            if (Session::get($class)) {
                $messages = Session::get($class);
                $messages = is_array($messages) ? $messages : [$messages];
            } else {
                $messages = [];
            }
            foreach ($messages as $message) {
                $msg[] = [
                    'text' => $message,
                    'class' => $class
                ];
            }
        }
        return $msg;
    }

}