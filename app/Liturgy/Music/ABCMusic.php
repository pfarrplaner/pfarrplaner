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

namespace App\Liturgy\Music;

use App\Liturgy\Song;
use Illuminate\Support\Facades\Storage;

class ABCMusic
{

    public const COLORS_NORMAL = 1;
    public const COLORS_INVERTED = 2;
    public const COLORS_GREENSCREEN = 3;

    /**
     * Get printable verses from a verse range string
     * @param Song $song Song
     * @param $rangeString Verse range string
     * @return array List of verse numbers
     */
    public static function getVerses(Song $song, $rangeString)
    {
        if ('refrain' == $rangeString) return [$rangeString];
        if (is_array($rangeString)) return $rangeString;
        $verseRefs = [];
        if (empty($rangeString)) {
            foreach ($song->verses as $verse) {
                $verseRefs[] = (int)$verse['number'];
            }
        } else {
            foreach (explode('+', $rangeString) as $range) {
                $subRange = explode('-', $range);
                if (!is_array($subRange)) {
                    $verseRefs[] = $subRange;
                } else {
                    if (count($subRange) == 1) {
                        $verseRefs[] = (int)$subRange[0];
                    } else {
                        if (is_numeric($subRange[0]) && is_numeric($subRange[1]) && ($subRange[0] < $subRange[1])) {
                            for ($i = $subRange[0]; $i <= $subRange[1]; $i++) {
                                $verseRefs[] = (int)$i;
                            }
                        } else {
                            $verseRefs[] = (int)$range;
                        }
                    }
                }
            }
        }
        return $verseRefs;
    }

    public static function make(Song $song, $verses = '', $lineNumber = null)
    {
        $music = self::makeHeaders($song, $lineNumber);
        $verses = self::getVerses($song, $verses);

        if (null !== $lineNumber) {
            $music .= self::makeSingleLine($song, $verses, $lineNumber);
        } else {
            for ($lineCtr = 0; $lineCtr < count(explode("\n", $song->notation)); $lineCtr++) {
                $music .= self::makeSingleLine($song, $verses, $lineCtr);
            }
        }
        return $music;
    }

    public static function makeHeaders(Song $song, $lineNumber = null)
    {
        $music = '';
        $headers = [
            'X' => 1,
            'L' => $song->note_length,
        ];
        if ($lineNumber == 0) {
            $headers['M'] = $song->measure ?: '4/4';
        }

        foreach ($headers as $key => $header) {
            $music .= $key . ':' . $header . "\n";
        }
        if ($song->prolog) {
            $music .= trim($song->prolog) . "\n";
        }
        $music .= 'K:' . $song->key . "\n";
        return $music;
    }

    public static function makeSingleLine(Song $song, $verses, $lineNumber, $showLineNumber = true)
    {
        $musicLines = explode("\n", $song->notation);
        $music = ($musicLines[$lineNumber] ?? '') . "\n";
        if (!is_array($verses)) {
            $verses = [$verses];
        }
        foreach ($verses as $verseNumber) {
            $verse = self::getVerse($song, $verseNumber);
            $textLines = explode("\n", $verse->notation);
            if (isset($textLines[$lineNumber])) {
                $music .= 'w:'
                    . ($showLineNumber ? $verse->number . '.~' : '')
                    . $textLines[$lineNumber] . "\n";
            }
        }

        return $music;
    }

    protected static function cachedFilePath(Song $song, $verses, $music, $colors, $lineNumber = null, $wildCard = false) {
        $parts = [
            $song->songbook_abbreviation,
            $song->reference,
            join('+', self::getVerses($song, $verses)),
            $colors,
            (null !== $lineNumber) ? $lineNumber : 'X',
            $wildCard ? '*' : md5($music),
        ];
        return storage_path(
            'app/music/abc/' . join('--', $parts).'.jpg'
        );
    }

    protected static function clearCache(Song $song, $verses, $colors, $lineNumber = null) {
        exec('rm '.self::cachedFilePath($song, $verses, '', $colors, $lineNumber, true));
    }

    public static function renderToFile(Song $song, $verses, $music, $colors = self::COLORS_NORMAL, $lineNumber = null)
    {
        Storage::makeDirectory('music/abc/');

        $cachedFileName = self::cachedFilePath($song, $verses, $music, $colors, $lineNumber);
        if (file_exists($cachedFileName)) return $cachedFileName;
        self::clearCache($song, $verses, $colors, $lineNumber);


        $temp = tempnam(sys_get_temp_dir(), 'pfp-abc-');
        $tempABC = $temp . '.abc';
        $tempPS = $temp . '.ps';
        $tempJPG = $temp . '.jpg';

        // build ABC
        file_put_contents($tempABC, $music);

        // build PS
        exec('abcm2ps -O ' . $tempPS . ' ' . $tempABC);
        unlink($tempABC);

        exec('gs -sDEVICE=jpeg -dJPEGQ=100 -dNOPAUSE -dBATCH -dSAFER -r300 -sOutputFile=' . $tempJPG . ' ' . $tempPS);
        unlink($tempPS);

        $params = ['-trim'];

        switch ($colors) {
            case self::COLORS_INVERTED:
                $params[] = '-channel RGB';
                $params[] = '-negate';
                break;
            case self::COLORS_GREENSCREEN:
                $params[] = '-channel RGB';
                $params[] = '-negate';
                $params[] = "-fill 'rgb(4,59,4)'";
                $params[] = "-opaque 'rgb(0,0,0)'";
                break;
        }

        exec('convert ' . $tempJPG . ' ' . join(' ', $params) . ' ' . $cachedFileName);

        return $cachedFileName;
    }

    protected static function getVerse(Song $song, $number)
    {
        foreach ($song->verses as $verse) {
            if ($verse->number == $number) {
                return $verse;
            }
        }
    }

    public static function images(Song $song, $verses, $colors = self::COLORS_NORMAL)
    {
        $images = [];

        $verses = self::getVerses($song, $verses);
        foreach ($verses as $verseNumber) {
            $verse = self::getVerse($song, $verseNumber);

            if ($verse->refrain_before) {
                $refrainLines = explode("\n", $song->refrain_notation);
                $refrainText = explode("\n", $song->refrain_text_notation);
                for ($i = 0; $i < count(explode("\n", $verse->refrain_notation)); $i++) {
                    $music = self::makeHeaders($song, $i).$refrainLines[$i]."\n".($refrainText[$i] ? 'w:'.$refrainText[$i]."\n" : '');
                    $images['refrain-before-'.$verseNumber.'-' . $i] = self::renderToFile($song, 'refrain', $music, $colors, $i);
                }
            }

            for ($i = 0; $i < count(explode("\n", $verse->notation)); $i++) {
                $music = self::makeHeaders($song, $i) . self::makeSingleLine($song, $verseNumber, $i, ($i == 0));
                $images[$verseNumber . '-' . $i] = self::renderToFile($song, $verse->number, $music, $colors, $i);
            }

            if ($verse->refrain_after) {
                $refrainLines = explode("\n", $song->refrain_notation);
                $refrainText = explode("\n", $song->refrain_text_notation);
                for ($i = 0; $i < count($refrainLines); $i++) {
                    $music = self::makeHeaders($song, $i).$refrainLines[$i]."\n".($refrainText[$i] ? 'w:'.$refrainText[$i]."\n" : '');
                    $images['refrain-after-'.$verseNumber.'-' . $i] = self::renderToFile($song, 'refrain', $music, $colors, $i);
                }
            }

        }
        return $images;
    }
}
