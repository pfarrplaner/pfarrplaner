<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/pfarrplaner/pfarrplaner
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

namespace App\Liturgy\LiturgySheets;


use App\Service;
use Illuminate\Support\Facades\Auth;

class SongMailLiturgySheet extends AbstractLiturgySheet
{

    protected $title = 'E-Mail mit Liederliste';
    protected $icon = 'fa fa-envelope';
    protected $isNotAFile = true;

    public function render(Service $service)
    {
        $subject = $service->titleText(false).' am '.$service->date->format('d.m.Y').', '.$service->timeText().', '.$service->locationText();

        $body = 'Hier meine Liederliste für den o.g. Gottesdienst:'.PHP_EOL.PHP_EOL;
        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if (($item->data_type == 'song') || ($item->data_type == 'psalm')) {
                    $body .= '  - '.$item->title.': '
                        .($item->data[$item->data_type]['songbook_abbreviation'] ?? $item->data[$item->data_type]['songbook'])
                        .' '
                        .$item->data[$item->data_type]['reference'].' '
                        .$item->data[$item->data_type]['title']
                        .(isset($item->data['verses']) && ($item->data['verses'] != '') ? ', '. $item->data['verses']: '')
                        .PHP_EOL;
                }
            }
        }

        $body .= PHP_EOL
            .'Der komplette Ablauf kann hier heruntergeladen werden:'.PHP_EOL
            .route('liturgy.download', ['service' => $service->slug, 'key' => 'A4']).PHP_EOL
            .PHP_EOL.'Freundliche Grüße, '.PHP_EOL.Auth::user()->name;

        $recipients = [];
        foreach ($service->organists as $organist) {
            $recipients[] = $organist->email;
        }
        if (count($recipients) == 0) $recipients[] = Auth::user()->email;

        return redirect('mailto:'.join(',', $recipients).'?subject='.rawurlencode($subject).'&body='.rawurlencode($body));
    }


}
