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

namespace App\Http\Controllers;


use App\Broadcast;
use App\Integrations\Youtube\YoutubeIntegration;
use App\Service;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class LiveChatController
 * @package App\Http\Controllers
 */
class LiveChatController
{

    /**
     * @param Service $service
     * @return Application|Factory|View
     */
    public function liveChat(Service $service)
    {
        return view('services.livechat.index', compact('service'));
    }

    /**
     * @param Service $service
     * @return JsonResponse
     */
    public function liveChatAjax(Service $service)
    {
        $youtube = YoutubeIntegration::get($service->city);
        $liveChatId = Broadcast::get($service)->getLiveBroadcast()->getSnippet()->getLiveChatId();
        $messages = $youtube->getLiveChat($liveChatId);
        return response()->json($messages);
    }

    /**
     * @param Request $request
     * @param Service $service
     * @return JsonResponse
     */
    public function liveChatPostMessage(Request $request, Service $service)
    {
        if (!$request->has('author')) {
            abort(500);
        }
        if (!$request->has('message')) {
            abort(500);
        }
        $msg = $request->get('message');
        $author = $request->get('author');

        $youtube = YoutubeIntegration::get($service->city);
        $liveChatId = Broadcast::get($service)->getLiveBroadcast()->getSnippet()->getLiveChatId();
        $youtube->sendLiveChatMessage($liveChatId, $author, $msg);
        return response()->json(compact('author', 'msg'));
    }


}

