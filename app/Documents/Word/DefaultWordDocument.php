<?php
/*
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2021 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
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


use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Exception\Exception;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Language;

class DefaultWordDocument
{
    /** @var PhpWord|null  */
    protected $phpWord = null;
    /** @var Section  */
    protected $section = null;

    public const NORMAL = 'Standard';
    public const BLOCKQUOTE = 'Zitat';
    public const BOLD = ['bold' => true];
    public const UNDERLINE = ['underline' => Font::UNDERLINE_SINGLE];
    public const BOLD_UNDERLINE = ['bold' => true, 'underline' => Font::UNDERLINE_SINGLE];

    public function __construct()
    {
        $this->phpWord = new PhpWord();
        $this->phpWord->getSettings()->setThemeFontLang(new Language(Language::DE_DE));
        $this->configureLayout();;
        $this->setDefaultDocumentStyles();
    }

    protected function configureLayout() {
        $this->section = $this->phpWord->addSection(
            [
                'orientation' => 'portrait',
                'pageSizeH' => Converter::cmToTwip(29.7),
                'pageSizeW' => Converter::cmToTwip(21),
                'marginTop' => Converter::cmToTwip(1.5),
                'marginBottom' => Converter::cmToTwip(1.5),
                'marginLeft' => Converter::cmToTwip(1.5),
                'marginRight' => Converter::cmToTwip(1.5),
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
                'lineHeight' => 1.08,
                'spaceBefore' => 0,
                'spaceAfter' => Converter::pointToTwip(8),
            ]
        );

        $this->phpWord->addFontStyle(self::NORMAL, [
            'name' => 'Helvetica Condensed',
            'size' => 11,
            'bold' => false,
            'italic' => false,
        ]);

        // Überschrift 1
        $this->phpWord->addTitleStyle(1, [
            'name' => 'Helvetica Condensed',
            'size' => 16,
            'bold' => true,
            'italic' => false,
        ], [
            'alignment' => Jc::START,
            'indentation' => [
                'left' => 0,
                'right' => 0,
                'firstLine' => 0,
                'hanging' => 0,
            ],
            'keepNext' => true,
            'lineHeight' => 1.08,
            'spaceBefore' => Converter::pointToTwip(12),
            'spaceAfter' => 0,
        ]);

        // Überschrift 2
        $this->phpWord->addTitleStyle(2, [
            'name' => 'Helvetica Condensed',
            'size' => 13,
            'bold' => true,
            'italic' => false,
        ], [
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
        ], [
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
                'left' => Converter::cmToTwip(1.52),
                'right' => Converter::cmToTwip(1.52),
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
    }

// SETTERS
    /**
     * @return PhpWord|null
     */
    public function getPhpWord(): ?PhpWord
    {
        return $this->phpWord;
    }

    /**
     * @param PhpWord|null $phpWord
     */
    public function setPhpWord(?PhpWord $phpWord): void
    {
        $this->phpWord = $phpWord;
    }

    /**
     * @return Section
     */
    public function getSection(): ?Section
    {
        return $this->section;
    }

    /**
     * @param Section $section
     */
    public function setSection(?Section $section): void
    {
        $this->section = $section;
    }


    /**
     * @param string $template
     * @param array $blocks
     * @param int $emptyParagraphsAfter
     * @param null $existingTextRun
     * @return TextRun|null
     */
    public function renderParagraph(
        $template = '',
        array $blocks = [],
        $emptyParagraphsAfter = 0,
        $existingTextRun = null
    ) {
        $textRun = $existingTextRun ?: $this->section->addTextRun($template);
        foreach ($blocks as $block) {
            $textRun->addText($block[0], $block[1]);
        }
        for ($i = 0; $i < $emptyParagraphsAfter; $i++) {
            $textRun = $this->section->addTextRun($template);
        }
        return $textRun;
    }


    /**
     * @param $filename
     * @throws Exception
     */
    public function sendToBrowser($filename)
    {
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter = IOFactory::createWriter($this->phpWord, 'Word2007');
        $objWriter->save('php://output');
        exit();
    }

    /**
     * Render some text with default formatting
     * @param $t Text
     * @param array $fontOption Font options
     * @param false $breakAfter Text break at the end?
     */
    public function renderNormalText($t, $fontOption = [], $breakAfter = false)
    {
        if (trim($t) == '') return;
        $paragraphs = explode("\n", trim($t));
        $textRun = $this->section->addTextRun(self::NORMAL);
        $ct = 0;
        foreach ($paragraphs as $paragraph) {
            $ct++;
            $textRun->addText($paragraph, $fontOption);
            if (($ct < count($paragraphs)) || $breakAfter) $textRun->addTextBreak();
        }
    }

}

