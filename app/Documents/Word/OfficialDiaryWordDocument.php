<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2022 Christoph Fischer, https://christoph-fischer.org
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

namespace App\Documents\Word;

use Carbon\Carbon;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\LineSpacingRule;
use PhpOffice\PhpWord\Style\Tab;

class OfficialDiaryWordDocument extends DefaultWordDocument
{

    protected function configureLayout()
    {
        $this->section = $this->phpWord->addSection(
            [
                'orientation' => 'portrait',
                'pageSizeH' => Converter::cmToTwip(29.7),
                'pageSizeW' => Converter::cmToTwip(21),
                'marginTop' => Converter::cmToTwip(1.7),
                'marginBottom' => Converter::cmToTwip(2.5),
                'marginLeft' => Converter::cmToTwip(2),
                'marginRight' => Converter::cmToTwip(1),
            ]
        );
    }


    protected function setDefaultDocumentStyles()
    {
        $this->phpWord->setDefaultFontName('Helvetica Condensed');
        $this->phpWord->setDefaultFontSize(11);

        // Standard
        $this->phpWord->setDefaultParagraphStyle(
            [
                'alignment' => Jc::START,
                'indentation' => [
                    'left' => 0,
                    'right' => 0,
                    'firstLine' => 0,
                    'hanging' => 0,
                ],
                'lineHeight' => 1.0,
                'spaceBefore' => 0,
                'spaceAfter' => 0,
            ]
        );

        $this->phpWord->addFontStyle(self::NORMAL, [
            'name' => 'Times New Roman',
            'size' => 12,
            'bold' => false,
            'italic' => false,
        ]);

        // Überschrift 1
        $this->phpWord->addTitleStyle(
            1,
            [
                'name' => 'Times New Roman',
                'size' => 18,
                'bold' => true,
                'italic' => false,
            ],
            [
                'alignment' => Jc::START,
                'indentation' => [
                    'left' => 0,
                    'right' => Converter::cmToTwip(.54),
                    'firstLine' => 0,
                    'hanging' => 0,
                ],
                'keepNext' => true,
                'lineHeight' => 1,
                'spacingLineRule' => LineSpacingRule::EXACT,
                'spacing' => Converter::pointToTwip(16),
                'spaceBefore' => 0,
                'spaceAfter' => Converter::pointToTwip(6),
            ]
        );

        // Überschrift 2
        $this->phpWord->addTitleStyle(2, [
            'name' => 'Helvetica Condensed',
            'size' => 13,
            'bold' => true,
            'italic' => false,
        ],                            [
                                          'alignment' => Jc::START,
                                          'indentation' => [
                                              'left' => 0,
                                              'right' => 0,
                                              'firstLine' => 0,
                                              'hanging' => 0,
                                          ],
                                          'keepNext' => true,
                                          'lineHeight' => 1.08,
                                          'spaceBefore' => Converter::pointToTwip(2),
                                          'spaceAfter' => 0,
                                      ]);

        // Überschrift 3
        $this->phpWord->addTitleStyle(3, [
            'name' => 'Helvetica Condensed',
            'size' => 12,
            'bold' => true,
            'italic' => false,
        ],                            [
                                          'alignment' => Jc::START,
                                          'indentation' => [
                                              'left' => 0,
                                              'right' => 0,
                                              'firstLine' => 0,
                                              'hanging' => 0,
                                          ],
                                          'keepNext' => true,
                                          'lineHeight' => 1.08,
                                          'spaceBefore' => Converter::pointToTwip(2),
                                          'spaceAfter' => 0,
                                      ]);

        // Zitat
        $this->phpWord->addParagraphStyle(self::BLOCKQUOTE, [
            'alignment' => Jc::BOTH,
            'indentation' => [
                'left' => Converter::cmToTwip(1),
                'right' => Converter::cmToTwip(1),
                'firstLine' => 0,
                'hanging' => 0,
            ],
            'lineHeight' => 1.08,
            'spaceBefore' => Converter::pointToTwip(10),
            'spaceAfter' => Converter::pointToTwip(8),
        ]);

        $this->phpWord->addFontStyle(self::BLOCKQUOTE, [
            'name' => 'Helvetica Condensed',
            'size' => 10,
            'bold' => false,
            'italic' => false,
        ]);

        // indented paragraph with instructions
        $this->phpWord->addParagraphStyle(self::INSTRUCTIONS, [
                                                                'alignment' => Jc::START,
                                                                'indentation' => [
                                                                    'left' => Converter::cmToTwip(1.27),
                                                                    'right' => 0,
                                                                    'firstLine' => 0,
                                                                    'hanging' => Converter::cmToTwip(1.27),
                                                                ],
                                                                'lineHeight' => 1.08,
                                                                'spaceBefore' => 0,
                                                                'spaceAfter' => Converter::pointToTwip(8),
                                                                'tabs' => [
                                                                    new Tab('left', Converter::cmToTwip(1.27)),
                                                                ],
                                                            ]
        );
    }

    public function render($start, $end, $diaryEntries)
    {
        $groupedDiaryEntries = $diaryEntries->groupBy('category');
        $dailyDiaryEntries = $diaryEntries->groupBy(function($entry) {
            return Carbon::parse($entry->date)->format('Y-m-d');
        });

        $this->getPhpWord()->addTableStyle(
            'table',
            [
                'borderSize' => 6,
                'borderColor' => '000000',
                'cellMargin' => 50,
            ],
            [
                'name' => 'Times New Roman'
            ]
        );

        $categoryGroups = [
            [
                'GTA' => "Gottesdienst\nTaufe und Abendmahl",
                'AMT' => 'Amtshandlungen',
                'SSD' => "Seelsorge\nDiakonie"
            ],
            [
                'UJU' => "Unterricht\nJugendarbeit",
                'BEB' => "Bibelarbeit\nErwachsenenbildung",
                'MGD' => "Mitarbeiterschaft\nGremien, Dienstbesprechung"
            ],
        ];

        foreach ($categoryGroups as $categoryIndex => $categoryGroup) {
            $this->getSection()->addTitle($start->formatLocalized('%B %Y'), 1);
            $table = $this->getSection()->addTable('table');
            $table->addRow();
            foreach ($categoryGroup as $categoryKey => $category) {
                $run = $table->addCell(Converter::cmToTwip(6))->addTextRun(OfficialDiaryWordDocument::NORMAL);
                $parts = explode("\n", $category);
                foreach ($parts as $index => $part) {
                    $run->addText($part, ['name' => 'Times New Roman']);
                    if ($index < count($parts)-1) $run->addTextBreak();
                }
            }
            $table->addRow(Converter::cmToTwip(23.4));
            foreach ($categoryGroup as $categoryKey => $category) {
                $run = $table->addCell(Converter::cmToTwip(6))->addTextRun(OfficialDiaryWordDocument::NORMAL);
                foreach (($groupedDiaryEntries[$categoryKey] ?? []) as $diaryEntry) {
                    $run->addText(Carbon::parse($diaryEntry->date)->format('d.m. H:i').' '.$diaryEntry->title, ['name' => 'Times New Roman']);
                    $run->addTextBreak();
                }
            }
        }
        $this->getSection()->addTitle($start->formatLocalized('%B %Y'), 1);

        $day = $start->copy();
        while ($day < $end) {
            $table = $this->getSection()->addTable('table');
            if (($day->day == 1) || ($day->dayOfWeek == 0)) {
                $table->addRow();
                $run1 = $table->addCell(Converter::cmToTwip(1.26))->addTextRun([
                                                                                   'alignment' => Jc::START,
                                                                                   'indentation' => [
                                                                                       'left' => 0,
                                                                                       'right' => 0,
                                                                                       'firstLine' => 0,
                                                                                       'hanging' => 0,
                                                                                   ],
                                                                                   'tabs' => [
                                                                                       new Tab('right', Converter::cmToTwip(.35)),
                                                                                       new Tab('left', Converter::cmToTwip(.5)),
                                                                                   ],
                                                                                   'keepNext' => true,
                                                                                   'lineHeight' => 1.0,
                                                                                   'spacingLineRule' => LineSpacingRule::EXACT,
                                                                                   'spacing' => Converter::pointToTwip(10),
                                                                                   'spaceBefore' => Converter::pointToTwip(.6),
                                                                                   'spaceAfter' => 0,
                                                                               ]);
                $run2 = $table->addCell(Converter::cmToTwip(16.83))->addTextRun([
                                                                                    'alignment' => Jc::START,
                                                                                    'indentation' => [
                                                                                        'left' => 0,
                                                                                        'right' => 0,
                                                                                        'firstLine' => 0,
                                                                                        'hanging' => 0,
                                                                                    ],
                                                                                    'keepNext' => true,
                                                                                    'lineHeight' => 1.0,
                                                                                    'spacingLineRule' => LineSpacingRule::EXACT,
                                                                                    'spacing' => Converter::pointToTwip(10),
                                                                                    'spaceBefore' => Converter::pointToTwip(.6),
                                                                                    'spaceAfter' => 0,
                                                                                ]);
            }
            $run1->addText("\t".$day->day."\t".$day->formatLocalized('%a'), ['name' => 'Times New Roman', 'size' => 10]);
            $run1->addTextBreak($day->dayOfWeek == 6 ? 1 : 2);

            $dayEvents = [];
            foreach ($dailyDiaryEntries[$day->format('Y-m-d')] ?? [] as $entry) {
                $dayEvents[] = Carbon::parse($entry->date)->format('H:i').' '.$entry->title;
            }
            $run2->addText(join('; ', $dayEvents), ['name' => 'Times New Roman', 'size' => 10]);
            $breakCount = ($day->dayOfWeek == 6 ? 1 : 2) - floor(strlen(join('; ', $dayEvents)) / 110);
            $run2->addTextBreak($breakCount);

            $day->addDay(1);
        }



    }


}
