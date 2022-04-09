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
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\SimpleType\JcTable;

class A4WordSpecificLiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Ablaufplan und Texte (DIN A4)';
    protected $icon = 'fa fa-file-word';

    /** @var Service $service  */
    protected $service = null;
    protected $extension = 'docx';
    protected $configurationPage = 'Liturgy/LiturgySheets/A4WordSpecificLiturgySheetConfiguration';
    protected $configurationComponent = 'A4WordSpecificLiturgySheetConfiguration';
    protected $currentRecipient = '';

    protected $defaultConfig = [
        'includeTexts' => 1,
        'includeTable' => 1,
        'includeSongTexts' => 1,
        'includeFullReadings' => 1,
        'includeAdditionalHeaders' => 1,
        'includeAllTexts' => 1,
        'recipients' => [],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function render(Service $service)
    {
        if (!$this->config['includeTexts']) {
            $this->config['includeAllTexts']
                = $this->config['includeAdditionalHeaders']
                = $this->config['includeSongTexts']
                = $this->config['includeFullReadings']
                = 0;
        }

        if ($this->config['includeAllTexts']) {
            $this->config['includeAdditionalHeaders'] = 1;
        }

        $this->service = $service;

        $doc = new DefaultWordDocument();
        $this->setProperties($doc);
        $doc->setInstructionsFontStyle(['size' => 8, 'italic' => true]);
        $doc->getPhpWord()->addTableStyle('Ablauf', ['borderSize' => 6, 'borderColor' => '444444', 'cellMargin' => 80, 'alignment' => JcTable::START]);

        if (count($this->config['recipients'])) {
            $first = true;
            foreach ($this->config['recipients'] as $recipient) {
                $this->currentRecipient = $recipient;
                $doc->setRecipient($recipient);
                if (!$first) $doc->getSection()->addPageBreak();
                $first = false;
                $this->renderLiturgyTable($doc, $recipient);

                if ($this->config['includeTexts']) {
                    // collect items for this user
                    $items = [];
                    $hasHeader = false;
                    foreach ($this->service->liturgyBlocks as $block) {
                        foreach ($block->items as $item) {
                            if ($this->config['includeAllTexts'] || in_array(
                                    $recipient,
                                    $item->recipients()
                                ) || $this->config['includeAdditionalHeaders']) {
                                if (!$hasHeader) {
                                    $doc->getSection()->addTitle('Texte für ' . $recipient, 1);
                                    $hasHeader = true;
                                }
                                $doc->getSection()->addTitle($item->title, 2);
                                if ($this->config['includeAllTexts'] && in_array($recipient, $item->recipients())) {
                                    $doc->getSection()->addText($recipient, ['italic' => true, 'fgColor' => 'yellow']);
                                }
                            }
                            if ($this->config['includeAllTexts']
                                || in_array($recipient, $item->recipients())
                                || ($this->config['includeSongTexts'] && (in_array($item->data_type, ['song', 'psalm']
                                    )))) {
                                if (method_exists($this, ($method = 'render' . ucfirst($item->data_type . 'Item')))) {
                                    $this->$method($doc, $item);
                                }
                            }
                        }
                    }
                }

            }
        } else {
            $this->renderLiturgyTable($doc);
        }

        $filename = $service->dateTime()->format('Ymd-Hi') . ' ' . $this->getFileTitle();
        $doc->sendToBrowser($filename);
    }

    protected function renderLiturgyTable(DefaultWordDocument $doc, $recipient = '') {
        $run = new TextRun($doc->getParagraphStyle('heading1'));
        $run->addText($this->service->titleText(false), $doc->getFontStyle('heading1'));
        $run->addTextBreak();
        $run->addText($this->service->dateTime()->formatLocalized('%d.%m.%Y, %H:%M Uhr').', '
                      .$this->service->locationText(), $doc->getFontStyle('heading1'));
        $doc->getSection()->addTitle($run, 0);

        if (!$this->config['includeTable']) return;

        if ($recipient) {
            $doc->renderNormalText('Ablaufplan für '.$recipient, ['italic' => true], true);
        }

        foreach ($this->service->liturgyBlocks as $block) {
            $doc->getSection()->addTitle($block->title, 1);

            $table = $doc->getSection()->addTable('Ablauf');
            foreach ($block->items as $item) {
                if (method_exists($this, ($method = 'render' . ucfirst($item->data_type . 'ItemRow')))) {
                    $table->addRow();
                    $this->$method($doc, $table, $item);
                }
            }
        }

    }

    protected function setProperties (DefaultWordDocument $doc) {
        $properties = $doc->getPhpWord()->getDocInfo();
        $properties->setCreator(Auth::user()->name);
        $properties->setCompany(Auth::user()->office ?? '');
        $properties->setTitle($this->getFileTitle());
        $properties->setDescription($this->getFileTitle().' ('.$this->title.')');
        $properties->setCategory('Gottesdienste');
        $properties->setLastModifiedBy(Auth::user()->name);
        $properties->setSubject('Ablauf und Texte');
    }

    public function getFileTitle(): string
    {
        return 'Gottesdienst' . (($this->service) && ($this->service->sermon) ? ' - ' . $this->service->sermon->title : '');
    }

    protected function renderRow(Table $table, Item $item, $text) {
        $table->addCell(Converter::cmToTwip(4.3))->addText($item->title, ['bold' => true]);
        $table->addCell(Converter::cmToTwip(8.5))->addText($text);
        $this->renderRecipients($table, $item);

    }

    protected function renderRecipients(Table $table, Item $item) {
        $cell = $table->addCell(Converter::cmToTwip(4.3));
        $run = $cell->addTextRun();
        $first = true;
        foreach ($item->recipients() as $recipient) {
            if (!$first) $run->addText(', ');
            $run->addText($recipient, $recipient == $this->currentRecipient ? ['fgColor' => 'yellow'] : []);
            $first = false;
        }
    }

    /**
     * @param DefaultWordDocument $doc
     * @param Table $table
     * @param Item $item
     */
    protected function renderFreetextItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        $text = '';
        if ($item->data['description']) {
            $text = (false !== strpos($item->data['description'], "\n")) ? explode("\n", $item->data['description'])[0] : substr($item->data['description'], 0, 80).'...';
        }
        $this->renderRow($table, $item, $text);
    }

    protected function renderLiturgicItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        /** @var LiturgicItemHelper $helper */
        $helper = $item->getHelper();
        $this->renderRow($table, $item, '');
    }

    protected function renderSermonItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        $this->renderRow($table, $item, $this->service->sermon_id ? $this->service->sermon->title : '');
    }

    protected function renderPsalmItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        /** @var PsalmItemHelper $helper */
        $helper = $item->getHelper();
        $this->renderRow($table, $item, $helper->getTitleText());
    }

    protected function renderSongItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        if (!isset($item->data['song'])) return;
        /** @var SongItemHelper $helper */
        $helper = $item->getHelper();
        $this->renderRow($table, $item, $helper->getTitleText());
    }

    protected function renderReadingItemRow(DefaultWordDocument $doc, Table $table, Item $item)
    {
        $this->renderRow($table, $item, $item->data['reference'] ?? '');
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
            if (isset($item->data['song']['song']['copyrights'])) {
                if ($item->data['song']['song']['copyrights']) {
                    $doc->renderNormalText($item->data['song']['song']['copyrights'], ['size' => 8]);
                }
            }
        }
        if (!$this->config['includeSongTexts']) return;

        foreach ($helper->getActiveVerses() as $verse) {
            if ($verse['refrain_before']) {
                $doc->renderNormalText($item->data['song']['song']['refrain'], ['italic' => true]);
            }
            $doc->renderNormalText($verse['number'].'. '.$verse['text']);
            if ($verse['refrain_after']) {
                $doc->renderNormalText($item->data['song']['song']['refrain'], ['italic' => true]);
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
