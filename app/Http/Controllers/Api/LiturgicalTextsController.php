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

namespace App\Http\Controllers\Api;

use App\Liturgy\Text;
use http\Env\Response;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\IOFactory;

class LiturgicalTextsController extends \App\Http\Controllers\Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Get a list of all liturgical texts
     * @return mixed
     */
    public function list()
    {
        return response()->json(Text::all());
    }

    /**
     * Import text from Word document
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importFromWord(Request $request)
    {
        if (!$request->hasFile('import')) {
            return response()->json('');
        }
        if (!file_exists($request->file('import')->getRealPath())) {
            return response()->json('');
        }

        $doc = IOFactory::load($request->file('import')->getRealPath());
        $text = '';
        $sections = $doc->getSections();
        foreach ($sections as $s) {
            $els = $s->getElements();
            /** @var ElementTest $e */
            foreach ($els as $e) {
                $class = get_class($e);
                if (method_exists($class, 'getText')) {
                    $text .= $e->getText();
                } else {
                    if (get_class($e) === 'PhpOffice\PhpWord\Element\TextRun') {
                        $textRunElements = $e->getElements();
                        foreach ($textRunElements as $textRunElement) {
                            $text .= $textRunElement->getText();
                        }
                        $text .= "\n";
                    } else {
                        $text .= "\n";
                    }
                }
            }

            unlink($request->file('import')->getRealPath());
            while (str_contains($text, "\n\n\n")) $text = str_replace("\n\n\n", "\n\n", $text);
            return response()->json(html_entity_decode(trim($text)));
        }
    }
}
