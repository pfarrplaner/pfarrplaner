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
use PhpOffice\PhpWord\Shared\Html;

class PsalmLiturgySheet extends AbstractLiturgySheet
{
    protected $title = 'Psalm';
    protected $icon = 'fa fa-file-word';
    protected $service = null;
    protected $extension = 'docx';
    protected $fileName = '';


    public function render(Service $service)
    {
        $this->service = $service;

        $doc = new DefaultA5WordDocument();

        foreach ($service->liturgyBlocks as $block) {
            foreach ($block->items as $item) {
                if (method_exists($this, ($method = 'render' . ucfirst($item->data_type . 'Item')))) {
                    $this->$method($doc, $item);
                    $doc->getSection()->addPageBreak();
                    $this->$method($doc, $item);
                }
            }
        }
        $doc->sendToBrowser($this->fileName);
    }

    protected function renderPsalmItem(DefaultWordDocument $doc, Item $item)
    {
        if (!$item->data['psalm']['text']) return;
        /** @var PsalmItemHelper $helper */
        $helper = $item->getHelper();
        $doc->getSection()->addTitle($item->data['psalm']['title'],1);
        $doc->renderNormalText($item->data['psalm']['text']);
        $this->fileName = $item->data['psalm']['title'];
    }

}
