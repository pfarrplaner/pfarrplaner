<?php


namespace App\Http\Controllers;


use App\Broadcast;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
use Illuminate\Http\Request;

class LiveChatController
{

    public function liveChat(Service $service) {
        return view('services.livechat.index', compact('service'));
    }

    public function liveChatAjax(Service $service) {
        $youtube = YoutubeIntegration::get($service->city);
        $liveChatId = Broadcast::get($service)->getLiveBroadcast()->getSnippet()->getLiveChatId();
        $messages = $youtube->getLiveChat($liveChatId);
        return response()->json($messages);
    }

    public function liveChatPostMessage(Request $request, Service $service) {
        if (!$request->has('author')) abort(500);
        if (!$request->has('message')) abort(500);
        $msg = $request->get('message');
        $author = $request->get('author');

        $youtube = YoutubeIntegration::get($service->city);
        $liveChatId = Broadcast::get($service)->getLiveBroadcast()->getSnippet()->getLiveChatId();
        $youtube->sendLiveChatMessage($liveChatId, $author, $msg);
        return response()->json(compact('author', 'msg'));
    }



}

