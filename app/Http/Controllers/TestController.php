<?php


namespace App\Http\Controllers;


use App\Mail\TestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class TestController extends Controller
{

    public function mail($address)
    {
        Log::debug('Sending test mail to '.$address);
        Mail::to($address)->bcc('dev@toph.de')->queue(new TestMail());
        return response('Testnachricht an '.$address.' versendet.', 200);
    }
}
