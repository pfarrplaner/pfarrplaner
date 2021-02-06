<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Liturgy\ItemHelpers;


use App\Baptism;
use App\Funeral;
use App\Service;
use App\Wedding;

class LiturgicItemHelper extends AbstractItemHelper
{

    public function getReplacedText(Service $service)
    {
        $text = $this->item->data['text'];
        if (($this->item->data['needs_replacement'] ?? '') == '') {
            return $text;
        }
        if (($this->item->data['replacement'] ?? '') == '') {
            return $text;
        }
        switch ($this->item->data['needs_replacement']) {
            case 'funeral':
                $funeralId = $this->item->data['replacement'];
                if ($service->funerals->pluck('id')->contains($funeralId)) {
                    $funeral = Funeral::find($funeralId);
                    list($lastName, $firstName) = explode(',', $funeral->buried_name);
                    foreach (
                        [
                            'verstorben:vorname' => trim($firstName),
                            'verstorben:lastname' => trim($lastName),
                            'verstorben:name' => trim($firstName).' '.trim($lastName),
                            'verstorben:todesdatum' => $funeral->dod ? $funeral->dod->format('d.m.Y') : 'verstorben:todesdatum',
                            'verstorben:geburtsdatum' => $funeral->dob ? $funeral->dob->format('d.m.Y') : 'verstorben:geburtsdatum',

                        ] as $marker => $value
                    ) {
                        $text = str_replace('[' . $marker . ']', $value, $text);
                    }
                }
                break;
            case 'baptism':
                $baptismId = $this->item->data['replacement'];
                if ($service->baptisms->pluck('id')->contains($baptismId)) {
                    $baptism = Baptism::find($baptismId);
                    list($lastName, $firstName) = explode(',', $baptismId->candidate_name);
                    foreach (
                        [
                            'taufe:vorname' => trim($firstName),
                            'taufe:lastname' => trim($lastName),
                            'taufe:name' => trim($firstName).' '.trim($lastName),

                        ] as $marker => $value
                    ) {
                        $text = str_replace('[' . $marker . ']', $value, $text);
                    }
                }
                break;
            case 'wedding':
                $weddingId = $this->item->data['replacement'];
                if ($service->weddings->pluck('id')->contains($weddingId)) {
                    $wedding = Wedding::find($weddingId);
                    list($lastName1, $firstName1) = explode(',', $wedding->spouse1_name);
                    list($lastName2, $firstName2) = explode(',', $wedding->spouse2_name);
                    foreach (
                        [
                            'trauung:vorname1' => trim($firstName1),
                            'trauung:lastname1' => trim($lastName1),
                            'trauung:name1' => trim($firstName1).' '.trim($lastName1),
                            'trauung:vorname2' => trim($firstName2),
                            'trauung:lastname2' => trim($lastName2),
                            'trauung:name2' => trim($firstName2).' '.trim($lastName2),

                        ] as $marker => $value
                    ) {
                        $text = str_replace('[' . $marker . ']', $value, $text);
                    }
                }
                break;
        }
        return $text;
    }

}
