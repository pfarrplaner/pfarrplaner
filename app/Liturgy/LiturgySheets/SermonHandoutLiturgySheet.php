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

namespace App\Liturgy\LiturgySheets;

use App\Documents\Word\DefaultWordDocument;
use App\Documents\Word\FoldedFlyerWordDocument;
use App\Helpers\ImageHelper;
use App\Liturgy\Item;
use App\Liturgy\ItemHelpers\PsalmItemHelper;
use App\Liturgy\ItemHelpers\SongItemHelper;
use App\Liturgy\Replacement\Replacement;
use App\Service;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Tab;

class SermonHandoutLiturgySheet extends AbstractLiturgySheet
{

    protected $title = 'Begleitblatt (Falzflyer DIN-lang)';
    protected $icon = 'fa fa-file-word';

    /** @var Service $service  */
    protected $service = null;
    protected $extension = 'docx';
//    protected $configurationPage = 'Liturgy/LiturgySheets/A4WordSpecificLiturgySheetConfiguration';
//    protected $configurationComponent = 'A4WordSpecificLiturgySheetConfiguration';

    public function render(Service $service)
    {

        $this->service = $service;

        $doc = new FoldedFlyerWordDocument();
        $this->setProperties($doc);

        $this->renderColumn1($doc);
        $this->renderColumn2($doc);
        $this->renderColumn3($doc);
        $this->renderColumn4($doc);
        $this->renderColumn5($doc);
        $this->renderColumn6($doc);







        $filename = $service->dateTime()->format('Ymd-Hi') . ' Begleitzettel zur Predigt';
        $doc->sendToBrowser($filename);

    }


    protected function setProperties (FoldedFlyerWordDocument $doc) {
        $properties = $doc->getPhpWord()->getDocInfo();
        $properties->setCreator(Auth::user()->name);
        $properties->setCompany(Auth::user()->office ?? '');
        $properties->setTitle($this->getFileTitle());
        $properties->setDescription($this->getFileTitle().' ('.$this->title.')');
        $properties->setCategory('Gottesdienste');
        $properties->setLastModifiedBy(Auth::user()->name);
        $properties->setSubject('Begleitzettel zur Predigt');
    }

    protected function title(FoldedFlyerWordDocument $doc, $text, $level = 0)
    {
        $run = new TextRun($doc->getParagraphStyle('heading'.($level+1)));
        $run->addText($text, $doc->getFontStyle('heading'.($level+1)));
        $doc->getSection()->addTitle($run, 0);
    }

    protected function renderColumn1(FoldedFlyerWordDocument $doc)
    {
        $this->title($doc, 'Aus unserer Gemeinde');
        $doc->renderText(Replacement::replaceAll('[termine]', $this->service),
                         [
                             'tabs' => [
                                 new Tab('left', Converter::cmToTwip(1.5)),
                             ],
                         ],
                         ['size' => 9]);
    }

    protected function renderColumn2(FoldedFlyerWordDocument $doc)
    {

        $doc->getPhpWord()->addTableStyle('col2TableStyle', ['afterSpacing' => 0, 'Spacing'=> 0, 'cellMargin'=>0]);
        $table = $doc->getSection()->addTable('col2TableStyle');
        $table->addRow(Converter::cmToTwip(8.87));
        $cell = $table->addCell(Converter::cmToTwip(8), ['valign' => 'top']);
        $run = $cell->addTextRun();

        $urls = [];
        foreach ($this->service->pastors as $pastor) {
            if ($pastor->own_website) {
                $urls[] = $pastor;
            }
        }
        if (count($urls)) {
            if (count($urls) == 1) {
                $run->addText('Weiteres Material zu dieser Predigt findest du online unter '.$urls[0]->own_website.'.');
            } else {
                $run->addText('Weiteres Material zu dieser Predigt findest du online unter:');
                foreach ($urls as $url) {
                    $run->addTextBreak();
                    $run->addText(' - '.$url->own_website.' ('.$url->fullName(true, true).')');
                }
            }
            $run->addTextBreak(2);
        }

        $podcasts = [];
        foreach ($this->service->pastors as $pastor) {
            if ($pastor->own_podcast_title) {
                $podcasts[] = $pastor;
            }
        }
        if (count($podcasts)) {
            if (count($podcasts) == 1) {
                $run->addText($this->podcastText($podcasts[0], true));
            } else {
                $run->addText('Die Aufnahme dieser Predigt ist zu hören:');
                foreach ($podcasts as $podcast) {
                    $run->addTextBreak();
                    $run->addText(' - '.$this->podcastText($podcast));
                }
            }
        }

        $table->addRow(Converter::cmToTwip(8.87));
        $cell = $table->addCell(Converter::cmToTwip(8), ['valign' => 'bottom']);
        $run = $cell->addTextRun(['align' => 'center']);
        $run->addImage(resource_path('img/logo/ELKW.png'), ['width' => Converter::cmToPoint(5)]);
        $run->addTextBreak(2);
        if ($this->service->city->official_title) {
            $run->addText(trim(str_replace('Evangelische', '', $this->service->city->official_title)),
                          ['bold' => true, 'size' => 9]);
        } else {
            $run->addText('Kirchengemeinde '.$this->service->city->name, ['bold' => true, 'size' => 9]);
        }
        $run->addText(trim(str_replace('Evangelische', '', $this->service->city->official_title)) ?? 'Kirchengemeinde '.$this->service->city->name,
        ['bold' => true, 'size' => 9]);

        foreach ($this->service->pastors as $pastor) {
            $run->addTextBreak(2);
            $run->addText($pastor->fullName(true, true), ['bold' => true, 'size' => 9]);
            if ($pastor->office) {
                $run->addTextBreak();
                $run->addText($pastor->office, ['size' => 9]);
            }
            if ($pastor->address) {
                $run->addTextBreak();
                $run->addText($pastor->address, ['size' => 9]);
            }
            if ($pastor->phone || $pastor->email || $this->service->city->homepage) {
                $run->addTextBreak();
                $parts = [];
                if ($pastor->phone) $parts[] = 'Fon '.$pastor->phone;
                if ($pastor->email) $parts[] = $pastor->email;
                $run->addText(join(' | ', $parts), ['size' => 9]);
            }
            if ($this->service->city->homepage) {
                $run->addTextBreak();
                $run->addText(parse_url($this->service->city->homepage)['host'], ['size' => 9]);
            }

        }

    }

    protected function renderColumn3(FoldedFlyerWordDocument $doc) {
        $doc->getPhpWord()->addTableStyle('col3TableStyle', ['afterSpacing' => 0, 'Spacing'=> 0, 'cellMargin'=>0]);
        $table = $doc->getSection()->addTable('col3TableStyle');
        $table->addRow(100);
        $table->addCell(Converter::cmToTwip(5.69));
        $cell = $table->addCell(Converter::cmToTwip(3.75), ['top' => 'bottom', 'borderLeftSize' => Converter::pointToTwip(1), 'borderLeftColor' => '951981']);

        $centerHeight = 15.5;
        if ($this->service->city->logo) {
            $cell->addTextRun(['align' => 'right'])
                ->addImage(storage_path('app/'.$this->service->city->logo), ['width' => Converter::cmToPoint(3)]);

            $centerHeight -= ImageHelper::getProportionalHeight(storage_path('app/'.$this->service->city->logo), 3);
        }
        $table->addRow(Converter::cmToTwip($centerHeight));
        $cell = $table->addCell(Converter::cmToTwip(9.75), ['gridSpan' => 2, 'valign' => 'center']);
        $run = $cell->addTextRun([]);
        $run->addText($this->service->sermon->title ?? 'Predigttitel', ['name' => 'Helvetica Condensed', 'size' => 20, 'bold' => true, 'color' => '951981']);
        if ($this->service->sermon->subtitle) {
            $run->addTextBreak();
            $run->addText($this->service->sermon->subtitle , ['name' => 'Helvetica Condensed', 'size' => 20, 'bold' => false, 'color' => '951981']);
        }
        if ($this->service->sermon->image) {
            $run->addTextBreak();
            $run->addImage(storage_path('app/'.$this->service->sermon->image), ['width' => Converter::cmToPoint(8)]);

            $license = ImageHelper::getEmbeddedLicenseString(storage_path('app/'.$this->service->sermon->image));
            if ($license) {
                $run->addTextBreak();
                $run->addText('Bild: '.$license, ['size' => 6, 'color' => '777777']);
            }
        }
        $run->addTextBreak(2);
        $run->addText($this->service->titleText(false, true));
        $run->addTextBreak();
        $run->addText($this->service->participantsText('P', true));
        $run->addTextBreak();
        $run->addText($this->service->day->date->format('d.m.Y').', '.$this->service->locationText());
        $table->addRow(Converter::cmToTwip(0.66));
        $table->addCell(Converter::cmToTwip(6));
        $textRun = $table->addCell(Converter::cmToTwip(3.75), ['valign' => 'bottom', 'borderLeftSize' => Converter::pointToTwip(1), 'borderLeftColor' => '951981'])
            ->addTextRun(['align' => 'right']);
        $textRun->addImage(resource_path('img/logo/ELKW.png'), ['width' => Converter::cmToPoint(3)]);

        $doc->getSection()->addColumnBreak();
    }

    protected function renderColumn4(FoldedFlyerWordDocument $doc) {
        $this->title($doc, 'Lieder & Texte');

        foreach ($this->service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if (method_exists($this, ($method = 'render' . ucfirst($item->data_type . 'Item')))) {
                    $this->$method($doc, $item);
                }
            }
        }


    }

    protected function renderColumn5(FoldedFlyerWordDocument $doc) {
    }

    protected function renderColumn6(FoldedFlyerWordDocument $doc) {
    }

    protected function podcastText($podcast, $fullText = false) {
        $sites = [];
        if ($podcast->own_podcast_itunes) $sites[] = 'iTunes';
        if ($podcast->own_podcast_spotify) $sites[] = 'Spotify';

        $text = $fullText ? 'Die Aufnahme dieser Predigt wird '.(count($sites) ? 'auf ' : ' im Podcast ') : (count($sites) ? 'Auf ' : 'Im Podcast ');
        if (count($sites)) {
            $text .= join(' und ', $sites).' unter ';
        }
        $text .= '"'.$podcast->own_podcast_title.'"';
        if ($fullText) $text .= ' veröffentlicht.';
        return $text;
    }


    protected function renderPsalmItem(DefaultWordDocument $doc, Item $item)
    {
        if (!isset($item->data['psalm'])) return;
        if (!$item->data['psalm']['text']) return;
        /** @var PsalmItemHelper $helper */
        $helper = $item->getHelper();
        $doc->renderNormalText($helper->getTitleText(), ['size' => 9, 'bold' => true]);
        $doc->renderNormalText($item->data['psalm']['text'], ['size' => 9]);
    }

    protected function renderSongItem(DefaultWordDocument $doc, Item $item)
    {
        if (!isset($item->data['song'])) return;
        if (null === $item->data['song']) return;
        /** @var SongItemHelper $helper */
        $helper = $item->getHelper();
        $doc->renderNormalText($helper->getTitleText(), ['size' => 9, 'bold' => true]);
        if ($item->data['song']['copyrights']) {
            $doc->renderNormalText($item->data['song']['copyrights'], ['size' => 7]);
        }

        foreach ($helper->getActiveVerses() as $verse) {
            if ($verse['refrain_before']) {
                $doc->renderNormalText($item->data['song']['refrain'], ['italic' => true, 'size' => 9]);
            }
            $doc->renderNormalText($verse['number'].'. '.$verse['text'],[ 'size' => 9]);
            if ($verse['refrain_after']) {
                $doc->renderNormalText($item->data['song']['refrain'], ['italic' => true, 'size' => 9]);
            }
        }
    }

    protected function renderSermonItem(DefaultWordDocument $doc, Item $item) {
        if (!$this->service->sermon) return;
        $doc->renderNormalText('Predigt: '.$this->service->sermon->title, ['size' => 9, 'bold' => true]);
        $doc->renderNormalText($this->service->sermon->reference, ['size' => 8, 'italic' => true]);
        $doc->renderNormalText($this->service->sermon->summary, ['size' => 9]);
    }


}
