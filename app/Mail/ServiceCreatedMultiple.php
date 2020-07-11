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

namespace App\Mail;

use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\View;

class ServiceCreatedMultiple extends AbstractServiceMailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->service->load(['location', 'day']);

        $ics = view('ical/ical', ['services' => $this->data['services'], 'token' => null, 'action' => null, 'key' => null])->render();
        $icsTitle = 'Gottesdienste.ics';

        return $this->subject('Mehrere neue Gottesdienste angelegt')
            ->view('mail.notifications.service-created-multiple')->with([
                'service' => $this->service,
                'user' => $this->user,
                'data' => $this->data,
            ])->attachData($ics, $icsTitle, [
                'mime' => 'text/calendar',
            ]);
    }
}
