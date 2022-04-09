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


use App\Liturgy;
use App\Liturgy\ItemHelpers\PsalmItemHelper;
use App\Liturgy\ItemHelpers\SongItemHelper;
use App\Liturgy\Music\ABCMusic;
use App\Service;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpPresentation\DocumentLayout;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;

class SongPPTLiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Powerpoint mit Liedern';
    protected $icon = 'fa fa-file-powerpoint';
    protected $extension = 'pptx';

    protected $configurationPage = 'Liturgy/LiturgySheets/SongPPTSongSheetConfiguration';
    protected $configurationComponent = 'SongPPTLiturgySheetConfiguration';

    protected $defaultConfig = [
        'textColor' => 'FFFFFFFF',
        'backgroundColor' => 'FF043b04',
        'backgroundColorEmpty' => 'FF043b04',
        'includeEmpty' => 1,
        'includeJingleAndIntro' => 1,
        'includeCredits' => 1,
        'verticalAlignment' => 'b',
        'fontSize' => 40,
        'renderMusic' => false,
    ];

    protected $counterColor = [
        'FFFFFFFF' => 'FF000000',
        'FF000000' => 'FFFFFFFF',
        'FF043b04' => 'FFFFFFFF',
    ];

    protected $musicColorSet = [
        'FFFFFFFF' => ABCMusic::COLORS_NORMAL,
        'FF000000' => ABCMusic::COLORS_INVERTED,
        'FF043b04' => ABCMusic::COLORS_GREENSCREEN,
    ];


    /** @var PhpPresentation */
    protected $ppt;

    public function render(Service $service)
    {
        $this->ppt = new PhpPresentation();
        $this->setDocumentProperties($service);
        $this->ppt->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9);
        $textColor = new Color($this->config['textColor']);
        $highlightedTextColor = new Color('FFF79646');
        $this->ppt->removeSlideByIndex(0);

        if ($this->config['includeEmpty']) {
            $this->slide();
        }
        if ($this->config['includeJingleAndIntro']) {
            $this->slide('Hier Jingle einfügen');
            $this->slide('Hier Intro einfügen');
        }
        if ($this->config['includeEmpty']) {
            $this->slide();
        }

        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if ($item->data_type == 'song') {
                    $this->renderSongItem($item);
                } elseif ($item->data_type == 'psalm') {
                    /** @var PsalmItemHelper $helper */
                    $helper = $item->getHelper();
                    foreach ($helper->getVerses() as $verse) $this->slide($verse, $this->config['fontSize']);
                } elseif ($item->data_type == 'liturgic') {
                    if ($item->title == 'Ehr sei dem Vater') {
//                        unset($slides[count($slides) - 1]);
                        $this->slide($item->data['text']);
                        if ($this->config['includeEmpty']) {
                            $this->slide();
                        }
                    }
                }
            }

        }
        if ($this->config['includeCredits']) {
            $this->creditsSlide($service->credits);
        }
        if ($this->config['includeJingleAndIntro']) {
            $this->slide('Hier Jingle einfügen', 18);
        }

        $fileName = $service->dateTime()->format('Ymd-Hi') . ' Texte und Lieder.pptx';
        return $this->sendToBrowser($fileName);
    }

    protected function renderSongItem(Liturgy\Item $item) {
        if ($this->config['renderMusic'] && (isset($item->data['song']['song']['notation']))) return $this->renderSongItemWithMusic($item);

        /** @var SongItemHelper $helper */
        $helper = $item->getHelper();
        $copyrights = $item->data['song']['song']['copyrights'] ?? '';
        if ($copyrights) {
            if ($item->data['song']['code']) {
                $copyrights = $item->data['song']['code'].' '.$item->data['song']['reference'].'. '.$copyrights;
            }
        }
        foreach ($helper->getActiveVerses() as $verse) {
            if ($verse['refrain_before']) {
                $this->slide($item->data['song']['song']['refrain'], $this->config['fontSize'], $this->config['textColor'], true,  $copyrights);
            }
            $this->slide($verse['number'].'. '.$verse['text'], $this->config['fontSize'], $this->config['textColor'], true,  $copyrights);
            if ($verse['refrain_after']) {
                $this->slide($item->data['song']['song']['refrain'], $this->config['fontSize'], $this->config['textColor'], true,  $copyrights);
            }
        }
        if ($this->config['includeEmpty']) {
            $this->slide();
        }
    }

    protected function renderSongItemWithMusic(Liturgy\Item $item) {
        $song = Liturgy\Song::find($item->data['song']['song_id']);
        $colorSet = $this->musicColorSet[$this->config['backgroundColor']];
        $images = ABCMusic::images($song, $item->data['verses'], $colorSet);

        foreach ($images as $key => $image) {
            $slide = $this->createEmptySlide($this->config['backgroundColor']);
            $shape = $slide->createDrawingShape()
                ->setName($item->data['song']['title'].' '.$key)
                ->setPath($image)
                ->setWidth(940)
                ->setOffsetX(10);
             $shape->setOffsetY(520-$shape->getHeight());
        }

        if ($this->config['includeEmpty']) {
            $this->slide();
        }
    }

    protected function slide($text = '', $size = -1, $rgb = -1, $bold = true, $copyrights = '', $backgroundColor = null)
    {
        if ($size == -1) $size = $this->config['fontSize'];
        if ($rgb == -1) $rgb = $this->config['textColor'];
        $color = new Color($rgb);
        $slide = $this->createEmptySlide($backgroundColor ?: ($text ? $this->config['backgroundColor'] : $this->config['backgroundColorEmpty']));
        if ($text) {
            if (!is_array($text)) $text = [$text];
            $text = str_replace("\n\t", "\r\t", $text);
            $shape = $this->createFullScreenRichTextShape($slide);
            $shape->setParagraphs([]);
            $ct = 0;
            foreach ($text as $line) {
                if ($ct=0) $paragraph = $shape->getActiveParagraph();
                else $paragraph = $shape->createParagraph();
                $ct++;
                if (substr($line,0, 1) == "\t") {
                    $paragraph->getAlignment()->setMarginLeft(35);
                    $line = substr($line, 1);
                }
                $paragraph->getAlignment()->setVertical($this->config['verticalAlignment']);
                $paragraph->getFont()->setBold($bold)->setSize($size)->setColor($color)->setName('Calibri');
                $paragraph->createTextRun(str_replace('&', '**', $line));
            }
        }
        if ($copyrights) {
            $shape = $slide->createRichTextShape()
                ->setWidth(950)
                ->setHeight(30)
                ->setOffsetX(10)
                ->setOffsetY(($text == '') ? 485: 505);
            $paragraph = $shape->getActiveParagraph();
            $paragraph->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $paragraph->getFont()->setBold(false)->setSize(10)->setColor($color)->setName('Calibri');
            $paragraph->createTextRun($copyrights);
        }
        return $slide;
    }

    protected function creditsSlide($credits)
    {
        $slide = $this->slide('', -1, $this->counterColor[$this->config['backgroundColorEmpty']], false,
                              $credits, $this->config['backgroundColorEmpty']);
    }

    protected function createEmptySlide($color): Slide
    {
        $backgroundColor = new Color($color);
        $backgroundColorBG = new \PhpOffice\PhpPresentation\Slide\Background\Color();
        $backgroundColorBG->setColor($backgroundColor);
        $slide = $this->ppt->createSlide();
        $slide->setBackground($backgroundColorBG);
        return $slide;
    }

    /**
     * Create a rich text shape covering the entire slide
     * @param Slide $slide
     * @return RichText
     */
    protected function createFullScreenRichTextShape(Slide $slide): RichText
    {
        $shape = $slide->createRichTextShape()
            ->setWidth(950)
            ->setHeight(500)
            ->setOffsetX(10)
            ->setOffsetY(0);
        return $shape;
    }

    /**
     * @param $filename
     * @throws Exception
     */
    protected function sendToBrowser($filename)
    {
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $objWriter = IOFactory::createWriter($this->ppt, 'PowerPoint2007');
        $objWriter->save('php://output');
        exit();
    }

    protected function setDocumentProperties(Service $service)
    {
        $titleLine = $service->titleText(false) . ' am ' . $service->dateText() . ', ' . $service->timeText(
            ) . ', ' . $service->locationText();
        $this->ppt->getDocumentProperties()
            ->setCreator(Auth::user()->name)
            ->setLastModifiedBy(Auth::user()->name)
            ->setTitle('Lieder und Texte')
            ->setSubject($titleLine)
            ->setDescription($titleLine)
            ->setCategory('Gottesdienst')
            ->setKeywords('Gottesdienst, Lieder, Psalm, Mitwirkende')
            ->setCompany('Evangelische Kirchengemeinde '.$service->city->name);
    }

}
