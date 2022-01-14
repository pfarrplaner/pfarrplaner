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

use PhpOffice\PhpWord\Shared\Converter;

class FoldedFlyerWordDocument extends DefaultWordDocument
{
    protected function configureLayout()
    {
        $this->section = $this->phpWord->addSection(
            [
                'orientation' => 'landscape',
                'pageSizeH' => Converter::cmToTwip(21),
                'pageSizeW' => Converter::cmToTwip(29.7),
                'marginTop' => Converter::cmToTwip(1.5),
                'marginBottom' => Converter::cmToTwip(1.5),
                'marginLeft' => Converter::cmToTwip(1.5),
                'marginRight' => Converter::cmToTwip(1.5),
                'colsNum' => 3,
                'breakType' => 'continuous',
            ]
        );
    }


    public function getFontStyle($style)
    {
        switch ($style) {
            case 'heading1':
                return [
                    'name' => 'Helvetica Condensed',
                    'size' => 20,
                    'bold' => false,
                    'italic' => false,
                    'color' => '951981',
                ];
            case 'heading2':
                return [
                    'name' => 'Helvetica Condensed',
                    'size' => 14,
                    'bold' => false,
                    'italic' => false,
                    'color' => '951981',
                ];
            case 'heading3':
                return [
                    'name' => 'Helvetica Condensed',
                    'size' => 12,
                    'bold' => false,
                    'italic' => false,
                    'color' => '951981',
                ];
            case 'heading4':
                return [
                    'name' => 'Helvetica Condensed',
                    'size' => 11,
                    'bold' => true,
                    'italic' => false,
                ];
        }
    }





}
