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

namespace App\Liturgy\Bible;

use App\Liturgy\Bible\ReferenceParser;

class BibleText
{

    protected $version = '';
    protected $text = [];
    protected $renderedVerses = [];

    public function __construct($version = 'LUT17')
    {
        $this->version=$version;
        $textFile = resource_path('bible/'.strtolower($version).'.txt');
        if (file_exists($textFile)) $this->text = explode("\n", file_get_contents($textFile));
    }

    public function get(array $reference): array {
        $result = [];
        $this->renderedVerses = [];
        foreach ($reference['parsed'] as $section) {
            $section['text'] = $this->getSection($section['range']);
            $result[] = $section;
        }
        return $result;
    }

    public function getSection(array $reference): array {
        $copy = false;
        $output = [];
        foreach ($this->text as $line) {
            if ($this->matches($line, $reference[0])) $copy = true;
            if ($reference[1]['verse']=='') {
                if ($copy && (!$this->matches($line, $reference[1]))) return $output;
                if ($data = $this->parse($line)) {
                    if ($copy && (!isset($this->renderedVerses[$data['referenceKey']]))) {
                        $output[$data['referenceKey']] = $data;
                        $this->renderedVerses[$data['referenceKey']] = $data;
                    }
                }
            } else {
                if ($data = $this->parse($line)) {
                    if ($copy && (!isset($this->renderedVerses[$data['referenceKey']]))) {
                        $output[$data['referenceKey']] = $data;
                        $this->renderedVerses[$data['referenceKey']] = $data;
                    }
                    if ($this->matches($line, $reference[1])) {
                        return $output;
                    }
                }
            }
        }
        return $output;
    }

    protected function parse(string $line) {
        $tmp = explode(' ', $line);
        $book = $tmp[0];
        if (!isset($tmp[1])) return;
        list($chapter, $verse) = explode(':',$tmp[1]);
        $slug = $book.' '.$chapter.':'.$verse;
        $bookTitle = ReferenceParser::getInstance()->getBookTitle($book);
        return [
            'book' => $book,
            'bookTitle' => $bookTitle,
            'referenceKey' => $bookTitle.'|'.$chapter.'|'.$verse,
            'referenceText' => $bookTitle.' '.$chapter.','.$verse,
            'chapter' => $chapter,
            'verse' => $verse,
            'text' => str_replace($slug.' ', '', $line),
            'slug' => $slug,
        ];
    }

    protected function matches(string $line, array $reference): bool {
        $ref = $reference['book'].' '.$reference['chapter'].':'.$reference['verse'];
        return (strtolower(substr($line, 0, strlen($ref))) == strtolower($ref));
    }

}
