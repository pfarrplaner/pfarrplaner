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

namespace App\Liturgy\Replacement;


use App\Baptism;
use App\Funeral;
use App\Imports\EventCalendarImport;
use App\Imports\OPEventsImport;
use App\Service;
use App\Tools\StringTool;
use App\Wedding;
use Carbon\Carbon;

class KasualienReplacer extends AbstractReplacer
{
    protected $description = 'Setzt automatisch die abzukündigenden Kasualien (Beerdigungen, Taufen, Trauungen) ein.';

    protected function getReplacementText(): string
    {
        $service = $this->service;
        $lastWeek = Carbon::createFromTimeString($service->date->format('Y-m-d') . ' 0:00:00 last Sunday');
        $nextWeek = Carbon::createFromTimeString($service->date->format('Y-m-d') . ' 0:00:00 next Sunday');

        $funerals = Funeral::where('announcement', $service->date->format('Y-m-d'))
            ->whereHas(
                'service',
                function ($query) use ($service) {
                    $query->where('city_id', $service->city->id);
                }
            )
            ->get();

        $weddings = Wedding::with('service')
            ->whereHas(
                'service',
                function ($query) use ($service, $nextWeek) {
                    $query->notHidden()->whereHas(
                        'day',
                        function ($query2) use ($service, $nextWeek) {
                            $query2->where('date', '>=', $service->date);
                            $query2->where('date', '<=', $nextWeek);
                            $query2->where('city_id', $service->city->id);
                        }
                    );
                }
            )->get();

        $baptisms = Baptism::with('service')
            ->whereHas(
                'service',
                function ($query) use ($service, $nextWeek) {
                    $query->notHidden()
                        ->whereHas(
                            'day',
                            function ($query2) use ($service, $nextWeek) {
                                $query2->where('date', '>=', $service->date);
                                $query2->where('date', '<=', $nextWeek);
                                $query2->where('city_id', $service->city->id);
                            }
                        );
                }
            )->get();

        $text = '';

        // Baptisms
        if (count($baptisms)) {
            $baptismArray = [];
            foreach ($baptisms as $baptism) {
                $baptismArray[$baptism->service->trueDate()->format('YmdHis')][] = $baptism;
            }
            ksort($baptismArray);

            foreach ($baptismArray as $baptisms) {
                $baptism = $baptisms[array_key_first($baptisms)];
                if ($baptism->service->id != $service->id) {
                    $text .= $this->renderParagraph();
                    if ($baptism->service->trueDate() == $service->trueDate()) {
                        $text .= $this->renderParagraph('Im Gottesdienst heute ' . $baptism->service->atText() . ' ' . (count(
                                        $baptisms
                                    ) > 1 ? 'werden' : 'wird') . ' getauft:');
                    } else {
                        $text .= $this->renderParagraph('Im Gottesdienst am ' . $baptism->service->date->format(
                                        'd.m.Y'
                                    ) . ' ' . $baptism->service->atText() . ' ' . (count(
                                        $baptisms
                                    ) > 1 ? 'werden' : 'wird') . ' getauft:');
                    }
                    foreach ($baptisms as $baptism) {
                        $text.=$this->renderParagraph($this->renderName($baptism->candidate_name) . ', ' . $baptism->candidate_address);
                    }
                }
            }
            $text .= $this->renderParagraph();
            $text .= $this->renderParagraph("*Christus hat der Kirche den Auftrag gegeben:\nGehet hin und machet zu Jüngern alle Völker\nund taufet sie auf den Namen des Vaters und\ndes Sohnes und des Heiligen Geistes.");
        }

        // weddings
        if (count($weddings)) {
            $weddingArray = [];
            foreach ($weddings as $wedding) {
                $weddingArray[$wedding->service->trueDate()->format('YmdHis')][] = $wedding;
            }
            ksort($weddingArray);

            foreach ($weddingArray as $weddings) {
                $wedding = $weddings[array_key_first($weddings)];
                if ($wedding->service->id != $service->id) {
                    $text .= $this->renderParagraph();
                    if ($wedding->service->trueDate() == $service->trueDate()) {
                        $text .= $this->renderParagraph('Im Gottesdienst heute ' . $wedding->service->atText(
                                    ) . ' werden kirchlich getraut:');
                    } else {
                        $text .= $this->renderParagraph('Im Gottesdienst am ' . $wedding->service->date->format(
                                        'd.m.Y'
                                    ) . ' ' . $wedding->service->atText() . ' werden kirchlich getraut:');
                    }
                    foreach ($weddings as $wedding) {
                        $text .= $this->renderParagraph($this->renderName($wedding->spouse1_name) . ' &amp; ' . $this->renderName(
                                        $wedding->spouse2_name));
                    }
                }
            }
            $text .= $this->renderParagraph();
            $text .= $this->renderParagraph("*Vater im Himmel,\nwir bitten für dieses Hochzeitspaar.\nBegleite sie auf ihrem gemeinsamen Weg.\nLass sie deine Liebe erfahren\nund stärke ihre Liebe zueinander\nin guten und in schweren Tagen.");
        }

        // funerals

        if (count($funerals)) {
            $funeralArray = ['past' => [], 'future' => []];
            foreach ($funerals as $funeral) {
                $key = ($funeral->service->trueDate() < $service->trueDate()) ? 'past' : 'future';
                $funeralArray[$key][] = $funeral;
            }

            if (count($funeralArray['past'])) {
                ksort($funeralArray['past']);
                $text .= $this->renderParagraph('Aus unserer Gemeinde ' . StringTool::pluralString(
                                count($funeralArray['past']),
                                'ist',
                                'sind'
                            ) . ' verstorben und '
                            . StringTool::pluralString(
                                count($funeralArray['past']),
                                'wurde',
                                'wurden'
                            ) . ' kirchlich bestattet:',);
                foreach ($funeralArray['past'] as $funeral) {
                    $text .= $this->renderParagraph($this->renderName($funeral->buried_name) . ', ' . $funeral->buried_address
                                . ($funeral->age() ? ', ' . $funeral->age() . ' Jahre' : '') . '.');
                }
                if (count($funeralArray['future'])) {
                    $text .= $this->renderParagraph();
                }
            }

            if (count($funeralArray['future'])) {
                ksort($funeralArray['future']);
                $text .= $this->renderParagraph('Aus unserer Gemeinde ' . StringTool::pluralString(
                                count($funeralArray['future']),
                                'ist',
                                'sind'
                            ) . ' verstorben:');
                foreach ($funeralArray['future'] as $funeral) {
                    $mode = $funeral->type;
                    if ($mode == 'Erdbestattung') {
                        $mode = 'Bestattung';
                    }
                    $text .= $this->renderParagraph($this->renderName($funeral->buried_name) . ', '
                                . $funeral->buried_address
                                . ($funeral->age() ? ', ' . $funeral->age() . ' Jahre' : '')
                                . '. Die ' . $mode . ' findet am ' . $funeral->service->date->formatLocalized(
                                    '%A, %d. %B'
                                )
                                . ' um ' . $funeral->service->timeText(true, '.')
                                . ' ' . $funeral->service->atText() . ' statt.');
                }
            }


            $text .= $this->renderParagraph();
            $text .= $this->renderParagraph(
                'Wir nehmen teil an der Trauer der Angehörigen und befehlen die Toten, die Trauernden und uns der Güte Gottes an.'
            );
            $text .= $this->renderParagraph();
            $text .= $this->renderParagraph('Wir bekennen gemeinsam:');
            $text .= $this->renderParagraph('Unser keiner lebt sich selber, und keiner stirbt sich selber.');
            $text .= $this->renderParagraph("Leben wir, so leben wir dem Herrn;\nsterben wir, so sterben wir dem Herrn.\nDarum: Wir leben oder sterben, so sind wir des Herrn.");
            $text .= $this->renderParagraph(
                "Denn dazu ist Christus gestorben und wieder lebendig geworden, dass er über Tote und Lebende Herr sei.\nAmen."
            );
        }

        return $text;
    }

    /**
     * @param $s
     * @return string
     */
    protected function renderName($s)
    {
        if (false !== strpos($s, ',')) {
            $t = explode(',', $s);
            $s = trim($t[1]) . ' ' . trim($t[0]);
        }
        return $s;
    }


}
