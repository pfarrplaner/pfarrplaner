<?php

namespace App\Http\Controllers;

use App\Liturgy\Bible\BibleText;
use App\Liturgy\Bible\ReferenceParser;
use Illuminate\Http\Request;

class BibleController extends Controller
{
    /**
     * Get a bible text from a reference
     * @param Request $request
     * @param $reference Reference
     * @param string $version Optional: Translation to be used
     * @return \Illuminate\Http\JsonResponse
     */
    public function text(Request $request, $reference, $version = 'LUT17')
    {
        $reference = str_replace('–', '-', $reference);
        $ref = ReferenceParser::getInstance()->parse($reference);
        $text = '';
        $bibleText = (new BibleText())->get($ref);

        $showVerseNumbers = $request->get('showVerseNumbers', true);
        $showReference = $request->get('showReference', false);

        foreach ($bibleText as $range) {
            foreach ($range['text'] as $verse) {
                $text .= ($showVerseNumbers ? $verse['verse'].' ' : '').$verse['text'].' ';
            }
        }
        if (($text) && ($showReference)) $text .= ' ('.$reference.')';

        return response()->json(['text' => $text, 'reference' => $ref]);
    }
}
