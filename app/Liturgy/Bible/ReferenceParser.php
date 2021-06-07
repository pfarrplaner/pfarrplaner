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


class ReferenceParser
{

    protected $map = [];
    protected $rawMap = [];

    /** @var ReferenceParser */
    protected static $instance = null;

    /**
     * @return ReferenceParser
     */
    public static function getInstance(): ReferenceParser
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->map = yaml_parse_file(resource_path('bible/BookMap.yaml'));
        $this->buildRawMap();
    }

    protected function buildRawMap()
    {
        foreach ($this->map as $code => $bookNames) {
            if (!is_array($bookNames)) {
                $bookNames = [$bookNames];
            }
            foreach ($bookNames as $bookName) {
                $this->rawMap[$bookName] = $code;
            }
        }
        // build shortened names
        foreach ($this->rawMap as $bookName => $code) {
            $stopAtLength = (is_numeric(substr($bookName, 0, 1)) ? 3 : 2);
            for ($i = strlen($bookName) - 1; $i > $stopAtLength; $i--) {
                $short = substr($bookName, 0, $i);

                if (isset($this->map[$short])) {
                    // this is an official book code, don't use and stop shortening
                    continue;
                } elseif (isset($this->rawMap[$short])) {
                    // conflict: this already matches other book
                    // --> delete completely (if it's not an official name) and stop shortening
                    if (!isset($this->map[$this->rawMap[$short]])) {
                        unset($this->rawMap[$short]);
                    }
                    continue;
                } else {
                    $this->rawMap[$short] = $code;
                }
            }
        }
    }

    /**
     * Get a parsed reference from a text
     *
     * Text reference may include optional verses in parentheses, like:
     * Jesaja 42,1-4 (5-9)
     *
     * originally based on:
     * @source https://stackoverflow.com/a/23720736
     * @author Jonny 5
     *
     * @param string $reference Reference string
     * @param bool $getOptionalReferenceInstead Return the full reference for the optional verses instead
     * @return array
     */
    public function parse(string $reference, bool $getOptionalReferenceInstead = false): array
    {
        $originalReference = $reference;

        // check for multiple commas: all but first should be replaced with periods
        $firstComma = strpos($reference, ',');
        $reference = substr($reference, 0, $firstComma+1).str_replace(',', '.', substr($reference, $firstComma+1));

        // consider parentheses: should be preceded and followed by periods
        $reference = strtr($reference, ['(' => '.(', ')' => ').']);

        // eliminate space after period
        $reference = trim(str_replace('. ', '.', $reference));

        // elliminate doubled periods
        $reference = trim(str_replace('..', '.', $reference));

        // removing trailing dot
        while (substr($reference, -1) == '.') $reference = substr($reference, 0, -1);


        // split verses from book chapters
        $parts = preg_split('/\s*,\s*/', trim($reference, " ;"));

        // init book
        $bible = array('book' => "", 'chapter' => "", 'verses' => array());

        // $part[0] = book + chapter, if isset $part[1] is verses
        if(isset($parts[0]))
        {
            // 1.) get chapter
            if(preg_match('/\d+\s*$/', $parts[0], $out)) {
                $bible['chapter'] = rtrim($out[0]);
            }

            // 2.) book name
            $bible['book'] = $this->matchBook(trim(preg_replace('/\d+\s*$/', "", $parts[0])));
        }

        // 3.) verses
        if(isset($parts[1])) {
            $bible['verses'] = preg_split('~\s*\.\s*~', $parts[1]);
        }

        $references = [];
        foreach ($bible['verses'] as $verse) {
            $ref = [];
            $ref['optional'] = (substr($verse, 0, 1) == '(');
            if ($ref['optional']) $verse = strtr($verse, ['('=> '', ')' => '']);
            if (false !== strpos($verse, '-')) {
                list($start,$end) = explode('-', $verse);
            } else {
                $start = $end = $verse;
            }
            $ref['range'] = [
                0 => [
                    'book' => $bible['book'],
                    'bookTitle' => $this->getBookTitle($bible['book']),
                    'chapter' => $bible['chapter'],
                    'verse' => $start,
                    'verseRaw' => $start,
                ],
                1 => [
                    'book' => $bible['book'],
                    'bookTitle' => $this->getBookTitle($bible['book']),
                    'chapter' => $bible['chapter'],
                    'verse' => $end,
                    'verseRaw' => $end,
                ],
            ];
            $references[] = $ref;
        }

        return ([
                'originalReference' => $originalReference,
                'parsed' => $references
            ]);
    }

    protected function matchBook($bookCode)
    {
        $book = $bookCode;
        foreach ($this->map as $key => $mappings) {
            if (!is_array($mappings)) {
                $mappings = [$mappings];
            }
            foreach ($mappings as $mapping) {
                if (substr(strtolower($mapping), 0, strlen($bookCode)) == strtolower($bookCode)) {
                    return $key;
                }
            }
        }
        return $book;
    }

    public function getBookTitle($bookCode)
    {
        if (!isset($this->map[$bookCode])) return '';
        $title = is_array($this->map[$bookCode]) ? $this->map[$bookCode][0] : $this->map[$bookCode];
        if (substr($title, 1, 1) == '.') {
            $title = substr($title, 0, 1) . '. ' . substr($title, 2);
        }
        return $title;
    }

    protected function renderSingleReference(
        array $reference,
        bool $optionalOpener = false,
        bool $renderBook = true,
        bool $renderChapter = true
    ) {
        $output = '';

        if ($renderBook) {
            $renderChapter = true;
        }
        if ($renderBook) {
            $output .= $reference['bookTitle'] . ' ';
        }
        if ($renderChapter) {
            $output .= $reference['chapter'] . (trim($reference['verse']) !== '' ? ',' : '');
        }
        return trim($output . ($optionalOpener ? '(' : '') . $reference['verseRaw']);
    }

    protected function renderRange(array $reference, bool $renderBook = true, bool $renderChapter = true)
    {
        $range = $reference['range'];
        $output = $this->renderSingleReference($range[0], $reference['optional'], $renderBook, $renderChapter);
        if ($range[1]['book'] !== $range[0]['book']) {
            $output .= '-' . $this->renderSingleReference($range[1], false);
        } elseif ($range[1]['chapter'] !== $range[0]['chapter']) {
            $output .= '-' . $this->renderSingleReference($range[1], false, false);
        } elseif ($range[1]['verse'] !== $range[0]['verse']) {
            $output .= '-' . $this->renderSingleReference($range[1], false, false, false);
        }
        return $output . ($reference['optional'] ? ')' : '');
    }

    public function render($reference): string
    {
        if (!is_array($reference)) {
            if (trim($reference) == '') {
                return '';
            }
            $reference = $this->parse($reference);
        }
        if ($reference == []) {
            return '';
        }
        $output = '';

        $previous = null;
        foreach ($reference['parsed'] as $section) {
            if (is_null($previous)) {
                $output .= $this->renderRange($section);
            } else {
                if ($previous['range'][0]['book'] !== $section['range'][0]['book']) {
                    $output .= '; ' . $this->renderRange($section);
                } elseif ($previous['range'][0]['chapter'] !== $section['range'][0]['chapter']) {
                    $output .= '; ' . $this->renderRange($section, false);
                } else {
                    $output .= '.' . $this->renderRange($section, false, false);
                }
            }
            $previous = $section;
        }

        return $output;
    }
}
