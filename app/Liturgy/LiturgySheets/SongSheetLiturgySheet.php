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

class SongSheetLiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Liedblatt';
    protected $icon = 'fa fa-file-word';
    protected $service = null;
    protected $extension = 'docx';

    public function __construct()
    {
        parent::__construct();
    }

    public function render(Service $service)
    {
        $this->service = $service;

        $doc = new DefaultWordDocument();
        $this->setProperties($doc);

        $run = new TextRun($doc->getParagraphStyle('heading1'));
        $run->addText($service->titleText(false), $doc->getFontStyle('heading1'));
        $run->addTextBreak();
        $run->addText($service->dateTime()->formatLocalized('%d.%m.%Y, %H:%M Uhr').', '
                      .$service->locationText(), $doc->getFontStyle('heading1'));
        $doc->getSection()->addTitle($run, 0);

        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
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
        $properties->setDescription($this->getFileTitle());
        $properties->setCategory('Gottesdienste');
        $properties->setLastModifiedBy(Auth::user()->name);
        $properties->setSubject('Liedblatt');
    }



    public function getFileTitle(): string
    {
        return 'Liedblatt';
    }

    protected function renderPsalmItem(DefaultWordDocument $doc, Item $item)
    {
        if (!isset($item->data['psalm'])) return;
        if (!$item->data['psalm']['text']) return;
        /** @var PsalmItemHelper $helper */
        $helper = $item->getHelper();
        $doc->getSection()->addTitle($helper->getTitleText(),3);
        $doc->renderNormalText($item->data['psalm']['text']);
    }

    protected function renderSongItem(DefaultWordDocument $doc, Item $item)
    {
        if (!isset($item->data['song'])) return;
        if (null === $item->data['song']) return;
        /** @var SongItemHelper $helper */
        $helper = $item->getHelper();
        $doc->getSection()->addTitle($helper->getTitleText(),3);
        if ($item->data['song']['copyrights']) {
            $doc->renderNormalText($item->data['song']['copyrights'], ['size' => 8]);
        }

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

}
