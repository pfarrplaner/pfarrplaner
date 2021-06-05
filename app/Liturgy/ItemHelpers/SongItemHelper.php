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

namespace App\Liturgy\ItemHelpers;


class SongItemHelper extends AbstractItemHelper
{

    public function getTitleText()
    {
        $title = $this->item->data['song']['songbook_abbreviation'] ?: $this->getItem()->data['song']['songbook'] ?: '';
        $title .= ' '.($this->item->data['song']['reference'] ?? '').' '.($this->item->data['song']['title'] ?? '');
        if ($this->item->data['verses']) $title.= ', '.$this->item->data['verses'];
        return trim(str_replace('  ', ' ', $title));
    }

    public function getActiveVerseNumbers()
    {
        $verseRefs = [];
        if (($this->item->data['verses'] ?? '') == '') {
            foreach ($this->item->data['song']['verses'] as $verse) $verseRefs[] = $verse['number'];
        } else {
            foreach (explode('+', $this->item->data['verses']) as $range) {
                $subRange = explode('-', $range);
                if (!is_array($subRange)) {
                    $verseRefs[] = $subRange;
                } else {
                    if (count($subRange) == 1) {
                        $verseRefs[] = $subRange[0];
                    } else {
                        if (is_numeric($subRange[0]) && is_numeric($subRange[1]) && ($subRange[0] < $subRange[1])) {
                            for ($i=$subRange[0]; $i<= $subRange[1]; $i++) $verseRefs[] = $i;
                        } else {
                            $verseRefs[] = $range;
                        }
                    }
                }
            }
        }
        return $verseRefs;
    }

    public function getActiveVerses()
    {
        $verses = [];
        foreach ($this->getActiveVerseNumbers() as $number) {
            foreach ($this->item->data['song']['verses'] ?? [] as $verse) {
                if ($verse['number'] == $number) $verses[] = $verse;
            }
        }
        return $verses;
    }


}
