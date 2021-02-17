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



use App\Broadcast;
use App\Service;
use Illuminate\Support\Facades\Auth;
use PDF;

class SongSheetLiturgySheet extends AbstractLiturgySheet
{

    protected $title = 'Liedblatt (DIN A4)';
    protected $icon = 'fa fa-file-pdf';

    public function render(Service $service)
    {
        $pdf = PDF::loadView(
            $this->getRenderViewName(),
            array_merge(compact('service'), $this->getData($service)),
            [],
            array_merge(
                [
                    'author' => isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email,
                ],
                $this->layout,
            ),
            $this->layout
        );

        $storageName = 'attachments/'.md5(time()).'.pdf';
        $content = $pdf->save(storage_path('app/'.$storageName));

        $service->update(['songsheet' => $storageName]);
        if (($service->youtube_url != '') && ($service->city->google_access_token != '')) {
            Broadcast::get($service)->update();
        }

        $fileName = $service->dateTime()->format('Ymd-Hi').' Lieder und Texte.pdf';
        return $pdf->download($fileName);
    }


}
