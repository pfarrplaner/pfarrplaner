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

namespace App\Liturgy\LiturgySheets;


use App\Documents\Word\DefaultA5WordDocument;
use App\Documents\Word\DefaultWordDocument;
use App\Liturgy\Bible\BibleText;
use App\Liturgy\Bible\ReferenceParser;
use App\Liturgy\Item;
use App\Liturgy\ItemHelpers\LiturgicItemHelper;
use App\Liturgy\ItemHelpers\PsalmItemHelper;
use App\Liturgy\ItemHelpers\SongItemHelper;
use App\Liturgy\Replacement\Replacement;
use App\Service;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Html;

class FullTextLiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Volltext';
    protected $icon = 'fa fa-file-word';

    /** @var Service $service  */
    protected $service = null;
    protected $extension = 'docx';
    protected $configurationPage = 'Liturgy/LiturgySheets/FullTextSongSheetConfiguration';
    protected $configurationComponent = 'FullTextLiturgySheetConfiguration';

    protected $defaultConfig = [
        'includeSongTexts' => 1,
        'includeFullReadings' => 1,
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function render(Service $service)
    {
        $this->service = $service;

        $doc = new DefaultA5WordDocument();
        $this->setProperties($doc);
        $doc->setInstructionsFontStyle(['size' => 8, 'italic' => true]);

        $run = new TextRun($doc->getParagraphStyle('heading1'));
        $run->addText($service->titleText(false), $doc->getFontStyle('heading1'));
        $run->addTextBreak();
        $run->addText($service->dateTime()->formatLocalized('%d.%m.%Y, %H:%M Uhr').', '
                      .$service->locationText(), $doc->getFontStyle('heading1'));
        $doc->getSection()->addTitle($run, 0);

        foreach ($service->liturgyBlocks as $block) {
            $doc->getSection()->addTitle($block->title, 1);
            foreach ($block->items as $item) {
                $doc->getSection()->addTitle($item->title, 2);
                if (method_exists($this, ($method = 'render' . ucfirst($item->data_type . 'Item')))) {
                    $this->$method($doc, $item);
                }
            }
        }
        $filename = $service->dateTime()->format('Ymd-Hi') . ' ' . $this->getFileTitle();
        $doc->sendToBrowser($filename);
    }

    protected function setProperties (DefaultWordDocument $doc) {
        $properties = $doc->getPhpWord()->getDocInfo();
        $properties->setCreator(Auth::user()->name);
        $properties->setCompany(Auth::user()->office ?? '');
        $properties->setTitle($this->getFileTitle());
        $properties->setDescription($this->getFileTitle().' ('.$this->title.')');
        $properties->setCategory('Gottesdienste');
        $properties->setLastModifiedBy(Auth::user()->name);
        $properties->setSubject('Komplette Liturgie');
    }

    public function getFileTitle(): string
    {
        return 'Gottesdienst' . (($this->service) && ($this->service->sermon) ? ' - ' . $this->service->sermon->title : '');
    }

    protected function renderFreetextItem(DefaultWordDocument $doc, Item $item)
    {
        if (!$item->data['description']) return;
        $doc->renderNormalText(Replacement::replaceAll($item->data['description'], $this->service));
    }

    protected function renderLiturgicItem(DefaultWordDocument $doc, Item $item)
    {
        /** @var LiturgicItemHelper $helper */
        $helper = $item->getHelper();
        $doc->renderNormalText(Replacement::replaceAll($helper->getReplacedText($this->service), $this->service));
    }

    protected function renderSermonItem(DefaultWordDocument $doc, Item $item)
    {
        if (null === $this->service->sermon) {
            $doc->renderNormalText('Für diesen Gottesdienst wurde noch keine Predigt angelegt.', ['italic' => true]);
        } else {
            if (!$this->service->sermon->text) return;
            $text = utf8_decode(strtr($this->service->sermon->text, [
                '<h1>' => '<h3>', '</h1>' => '</h3>',
                '<h2>' => '<h4>', '</h2>' => '</h4>',
            ]));
            $dom = new \DOMDocument();
            $dom->loadHTML($text, LIBXML_NOWARNING);
            $nodes = [];
            /** @var \DOMNode $node */
            foreach ($dom->documentElement->firstChild->childNodes as $node) {
                if ($node->nodeName == 'blockquote') {
                    $doc->renderText($node->nodeValue, $doc::BLOCKQUOTE, ['size' => 10]);
                } else {
                    Html::addHtml($doc->getSection(), '<body>'.str_replace('<br>', '<br />', $dom->saveHTML($node)).'</body>');
                }
                $nodes[] = $node->nodeName.' -> '.$node->nodeValue;
            }
        }
    }

    protected function renderPsalmItem(DefaultWordDocument $doc, Item $item)
    {
        if (!$item->data['psalm']['text']) return;
        /** @var PsalmItemHelper $helper */
        $helper = $item->getHelper();
        $doc->getSection()->addTitle($helper->getTitleText(),3);
        $doc->renderNormalText($item->data['psalm']['text']);
    }

    protected function renderSongItem(DefaultWordDocument $doc, Item $item)
    {
        if (!isset($item->data['song'])) return;
        /** @var SongItemHelper $helper */
        $helper = $item->getHelper();
        $doc->getSection()->addTitle($helper->getTitleText(),3);
        if (isset($item->data['song'])) {
            if (isset($item->data['song']['copyrights'])) {
                if ($item->data['song']['copyrights']) {
                    $doc->renderNormalText($item->data['song']['copyrights'], ['size' => 8]);
                }
            }
        }
        if (!$this->config['includeSongTexts']) return;

        foreach ($helper->getActiveVerses() as $verse) {
            if ($verse['refrain_before']) {
                $doc->renderNormalText($item->data['song']['refrain'], ['italic' => true]);
            }
            $doc->renderNormalText($verse['number'].'. '.$verse['text']);
            if ($verse['refrain_after']) {
                $doc->renderNormalText($item->data['song']['refrain'], ['italic' => true]);
            }
        }
    }

    protected function renderReadingItem(DefaultWordDocument $doc, Item $item)
    {
        if (!$item->data['reference']) return;
        $doc->getSection()->addTitle($item->data['reference'], 3);
        if (!$this->config['includeFullReadings']) return;

        $ref = ReferenceParser::getInstance()->parse($item->data['reference']);
        $bibleText = (new BibleText())->get($ref);

        $run = [];
        foreach ($bibleText as $range) {
            foreach ($range['text'] as $verse) {
                $run[] = [$verse['verse'].' ', ['superScript' => true]];
                $run[] = [$verse['text']."\n", []];
            }
        }

        $doc->renderParagraph($doc::NORMAL, $run);

    }

}
