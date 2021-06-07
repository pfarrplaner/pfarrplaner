<?php

namespace App\Http\Controllers;

use App\Liturgy\Bible\BibleText;
use App\Liturgy\Bible\ReferenceParser;
use Illuminate\Http\Request;

class BibleController extends Controller
{
    /**
     * Get a bible text from a reference
     * @param $reference Reference
     * @param string $version Optional: Translation to be used
     * @return \Illuminate\Http\JsonResponse
     */
    public function text($reference, $version = 'LUT17')
    {
        $reference = str_replace('–', '-', $reference);
        $ref = ReferenceParser::getInstance()->parse($reference);
        $text = '';
        $bibleText = (new BibleText())->get($ref);

        foreach ($bibleText as $range) {
            foreach ($range['text'] as $verse) {
                $text .= $verse['verse'].' '.$verse['text'].' ';
            }
        }

        return response()->json(['text' => $text, 'reference' => $ref]);
    }
}
