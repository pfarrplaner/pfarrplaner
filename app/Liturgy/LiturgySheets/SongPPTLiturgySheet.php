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

namespace App\Liturgy\LiturgySheets;


use App\Liturgy\ItemHelpers\PsalmItemHelper;
use App\Liturgy\ItemHelpers\SongItemHelper;
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

    /** @var PhpPresentation */
    protected $ppt;

    public function render(Service $service)
    {
        $this->ppt = new PhpPresentation();
        $this->setDocumentProperties($service);
        $this->ppt->getLayout()->setDocumentLayout(DocumentLayout::LAYOUT_SCREEN_16X9);
        $white = new Color('FFFFFFFF');
        $orange = new Color('FFF79646');
        $this->ppt->removeSlideByIndex(0);

        $this->slide();
        $this->slide('Hier Jingle einfügen');
        $this->slide('Hier Intro einfügen');
        $this->slide();

        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if ($item->data_type == 'song') {
                    /** @var SongItemHelper $helper */
                    $helper = $item->getHelper();
                    foreach ($helper->getActiveVerses() as $verse) {
                        if ($verse['refrain_before']) {
                            $this->slide($item->data['song']['refrain']);
                        }
                        $this->slide($verse['number'].'. '.$verse['text']);
                        if ($verse['refrain_after']) {
                            $this->slide($item->data['song']['refrain']);
                        }
                    }
                    $this->slide();
                } elseif ($item->data_type == 'psalm') {
                    /** @var PsalmItemHelper $helper */
                    $helper = $item->getHelper();
                    foreach ($helper->getVerses() as $verse) $this->slide($verse, 30);
                } elseif ($item->data_type == 'liturgic') {
                    if ($item->title == 'Ehr sei dem Vater') {
//                        unset($slides[count($slides) - 1]);
                        $this->slide($item->data['text']);
                    }
                }
            }

        }
        $this->slide($service->credits, 18, 'FFFFFFFF', false);
        $this->slide('Hier Jingle einfügen', 18);

        $fileName = $service->dateTime()->format('Ymd-Hi') . ' Texte und Lieder.pptx';
        return $this->sendToBrowser($fileName);
    }

    protected function slide($text = '', $size = 30, $rgb = 'FFFFFFFF', $bold = true)
    {
        $slide = $this->createEmptySlide();
        if ($text) {
            if (!is_array($text)) $text = [$text];
            $text = str_replace("\n\t", "\r\t", $text);
            $shape = $this->createFullScreenRichTextShape($slide);
            $color = new Color($rgb);
            $ct = 0;
            foreach ($text as $line) {
                if ($ct=0) $paragraph = $shape->getActiveParagraph();
                else $paragraph = $shape->createParagraph();
                $ct++;
                if (substr($line,0, 1) == "\t") {
                    $paragraph->getAlignment()->setMarginLeft(35);
                    $line = substr($line, 1);
                }
                $paragraph->getFont()->setBold($bold)->setSize($size)->setColor($color)->setName('Calibri');
                $paragraph->createTextRun(str_replace('&', '**', $line));
            }
        }
    }

    protected function createEmptySlide(): Slide
    {
        $black = new Color('FF043b04');
        $blackBG = new \PhpOffice\PhpPresentation\Slide\Background\Color();
        $blackBG->setColor($black);
        $slide = $this->ppt->createSlide();
        $slide->setBackground($blackBG);
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
            ->setWidth(960)
            ->setHeight(540)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_BOTTOM);
        return $shape;
    }

    /**
     * @param $filename
     * @throws Exception
     */
    protected function sendToBrowser($filename)
    {
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $filename . '.pptx"');
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
