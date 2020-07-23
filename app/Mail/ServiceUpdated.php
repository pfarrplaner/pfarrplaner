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

/**
 * Class ServiceUpdated
 * @package App\Mail
 */
class ServiceUpdated extends AbstractServiceMailable
{

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param Service $service
     * @param User $originatingUser
     * @param array $data
     */
    public function __construct(User $user, Service $service, User $originatingUser, array $data)
    {
        parent::__construct($user, $service, $originatingUser, $data);
        $this->changed = $service->changed;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $participants = [];
        if (isset($this->changed['participants'])) {
            foreach (['original', 'changed'] as $type) {
                foreach ($this->changed['participants'][$type] as $participant) {
                    $category = $participant['pivot']['category'];
                    switch ($category) {
                        case 'P':
                            $category = 'Pfarrer*in';
                            break;
                        case 'O':
                            $category = 'Organist*in';
                            break;
                        case 'M':
                            $category = 'Mesner*in';
                            break;
                        case 'A':
                            $category = 'Andere Beteiligte';
                            break;
                    }
                    $participants[$category][$type][] = $participant;
                }
            }
        }
        $tmpParticipants = $participants;
        foreach ($tmpParticipants as $category => $p) {
            if (print_r($p['original'] ?? [], 1) == print_r($p['changed'] ?? [], 1)) {
                unset($participants[$category]);
            }
        }

        $ics = view(
            'ical/ical',
            ['services' => [$this->service], 'token' => null, 'action' => null, 'key' => null]
        )->render();
        $icsTitle = 'GD ' . $this->service->day->date->format('Ymd') . ' ' . $this->service->timeText(
                false
            ) . ' ' . $this->service->locationText() . '.ics';
        return $this->subject(
            'Änderungen am einem Gottesdienst vom ' . $this->service->dateText() . ', ' . $this->service->timeText(
            ) . ' (' . $this->service->locationText() . ')'
        )
            ->view('mail.notifications.service-update')->with(
                [
                    'service' => $this->service,
                    'changes' => $this->changed,
                    'participants' => $participants,
                    'user' => $this->user,
                    'originatingUser' => $this->originatingUser,
                ]
            )->attachData(
                $ics,
                $icsTitle,
                [
                    'mime' => 'text/calendar',
                ]
            );
    }
}
