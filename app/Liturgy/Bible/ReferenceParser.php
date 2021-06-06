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
            for ($i = strlen($bookName) - 1; $i > 2; $i--) {
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

    protected function replaceBookNamesWithCodes(string $reference): string
    {
        foreach ($this->rawMap as $bookName => $code) {
            $reference = str_replace($bookName, '###' . $code . '###', $reference);
        }
        return $reference;
    }

    protected function replaceBookCodesWithNames(string $reference): string
    {
        preg_match_all("/###(.*)###/", $reference, $matches);
        foreach ($matches[1] as $match) {
            $book = $this->map[$match];
            if (is_array($book)) {
                $book = $book[0];
            }
            $reference = str_replace('###' . $match . '###', $book, $reference);
        }
        return $reference;
    }

    /**
     * Get a parsed reference from a text
     *
     * Text reference may include optional verses in parentheses, like:
     * Jesaja 42,1-4 (5-9)
     *
     * @param string $reference Reference string
     * @param bool $getOptionalReferenceInstead Return the full reference for the optional verses instead
     * @return array
     */
    public function parse(string $reference, bool $getOptionalReferenceInstead = false): array
    {
        $originalReference = trim($reference);
        $reference = strtr(
            trim($originalReference),
            [
                '. ' => '.',
                ' -' => '-',
                '- ' => '-',
                ')' => '.',
                '. ' => '.',
                ' (' => '.(',
                '(' => '.(',
                ' ;' => '.',
                ';' => '.',
                '..' => '.'
            ]
        );
        $reference = str_replace(',.', ',', $reference);
        $reference = $this->replaceBookNamesWithCodes($reference);
        $references = explode('.', $reference);

        $defaultValues = [];
        foreach ($references as $refKey => $referenceSection) {
            $parsed = [];
            $optional = strpos($referenceSection, '(') !== false;
            $referenceSection = str_replace('(', '', $referenceSection);
            foreach (explode('-', $referenceSection) as $singleReference) {
                $singleReference = $this->parseSingleReference($singleReference, $defaultValues);
                $parsed[] = $defaultValues = $singleReference;
            }
            if (count($parsed) == 1) {
                $parsed[1] = $parsed[0];
            }
            $references[$refKey] = [
                'range' => $parsed,
                'optional' => (bool)$optional
            ];
        }
        return [
            'originalReference' => $originalReference,
            'parsed' => $references
        ];
    }

    protected function parseSingleReference(string $reference, array $defaults): array
    {
        $reference = trim($this->replaceBookCodesWithNames($reference));
        $reference = str_replace(', ', ',', $reference);

        if (strpos($reference, ' ')) {
            list($book, $tmp) = explode(' ', $reference);
            $reference = trim(str_replace($book, '', $reference));
        } else {
            $book = $defaults['book'];
        }
        if (strpos($reference, ',') !== false) {
            list ($chapter, $verse) = explode(',', $reference);
        } else {
            if ($defaults['chapter']) {
                $chapter = $defaults['chapter'];
                $verse = $reference;
            } else {
                $chapter = $reference;
                $verse = '';
            }
        }
        $book = $this->matchBook($book);
        $verseNumeric = filter_var($verse, FILTER_SANITIZE_NUMBER_INT);
        if (trim($verseNumeric) == '') {
            $verseNumeric = $defaults['verse'];
        }
        return [
            'book' => $book,
            'bookTitle' => $this->getBookTitle($book),
            'chapter' => $chapter,
            'verseRaw' => $verse,
            'verse' => $verseNumeric
        ];
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
