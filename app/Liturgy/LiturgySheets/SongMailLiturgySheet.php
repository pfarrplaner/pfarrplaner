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


use App\Liturgy\ItemHelpers\SongItemHelper;
use App\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Pluralizer;
use Illuminate\Support\Str;

class SongMailLiturgySheet extends AbstractLiturgySheet
{

    protected $title = 'E-Mail mit Liederliste';
    protected $icon = 'fa fa-envelope';
    protected $isNotAFile = true;

    public function render(Service $service)
    {
        $subject = $service->titleText(false) . ' am ' . $service->date->isoFormat(
                'dddd, DD.MM.YYYY'
            ) . ', ' . $service->timeText() . ', ' . $service->locationText();

        $body = 'Hier der geplante Ablauf für den Gottesdienst' . ' am ' . $service->date->isoFormat('dddd, DD. MMMM YYYY')
            . ', ' . $service->timeText() .
            ($service->location->at_text ? ' '.$service->location->at_text : ', '.$service->locationText())
            . ':' . PHP_EOL . PHP_EOL;
        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if (($item->data_type == 'song') && (isset($item->data['song']))) {
                    $helper = new SongItemHelper($item);
                    $verseCount = $helper->getActiveVerseCount();

                    $body .= '  -> ' . $item->title . ': '
                        . ($item->data[$item->data_type]['code'] ?? $item->data[$item->data_type]['songbook']['name'] ?? '')
                        . ' '
                        . $item->data[$item->data_type]['reference'] . ' '
                        . ($item->data[$item->data_type]['altEG'] ? '(EG ' . $item->data[$item->data_type]['altEG'] . ') ' : '')
                        . $item->data[$item->data_type]['song']['title']
                        . $helper->forceVerseString(', ')
                        . ($verseCount ? ' ('.$verseCount.' '.($verseCount > 1 ? 'Strophen' : 'Strophe').')' : '')
                        . PHP_EOL;
                } elseif ($item->data_type == 'psalm') {
                    if (isset($item->data['psalm'])) {
                        $body .= '  -> ' . $item->title . ': '
                            . ($item->data[$item->data_type]['songbook_abbreviation'] ?? $item->data[$item->data_type]['songbook'] ?? '')
                            . ' '
                            . $item->data[$item->data_type]['reference'] . ' '
                            . $item->data[$item->data_type]['title']
                            . (isset($item->data['verses']) && ($item->data['verses'] != '') ? ', ' . $item->data['verses'] : '')
                            . PHP_EOL;
                    }
                } else {
                    if (isset($item->data['responsible'])) {
                        $concernsOrganists = in_array('ministry:organists', $item->data['responsible']);
                        if (!$concernsOrganists) {
                            foreach ($service->organists as $organist) {
                                $concernsOrganists = $concernsOrganists || in_array('user:'.$organist->id, $item->data['responsible']);
                            }
                        }
                    } else {
                        $concernsOrganists = false;
                    }

                    if ($concernsOrganists) {
                        $body .= '  -> ' .$item->title.PHP_EOL;
                    } else {
                        $body .= '        '.$item->title.PHP_EOL;
                    }
                }
            }
        }

        $body .= PHP_EOL
            . 'Der komplette Ablauf kann hier in einem druckbaren Format heruntergeladen werden:' . PHP_EOL
            . route('liturgy.download', ['service' => $service->slug, 'key' => 'A4']) . PHP_EOL
            . PHP_EOL . 'Freundliche Grüße, ' . PHP_EOL . Auth::user()->name;

        $recipients = [];
        foreach ($service->organists as $organist) {
            $recipients[] = $organist->email;
        }

        if (count($recipients) == 0) {
            $recipients[] = Auth::user()->email;
        }


        return redirect(
            'mailto:' . join(',', $recipients) . '?subject=' . rawurlencode($subject) . '&body=' . rawurlencode($body)
        );
    }
}
