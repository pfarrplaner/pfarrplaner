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

class A4LiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Ablaufplan (DIN A4)';
    protected $icon = 'fa fa-file-pdf';

    protected function getData(Service $service) {
        // temporary override:
        return ['recipients' => []];

        if (request()->has('noRecipients')) return ['recipients' => []];

        $recipients = [];
        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                $recipients = array_merge($item->recipients(), $recipients);
            }
        }
        $recipients2 = array_unique($recipients);
        $recipients = [];
        foreach ($recipients2 as $recipient) {
            if ($recipient != '') $recipients[] = $recipient;
        }
        sort($recipients);
        return compact('recipients');
    }
}
