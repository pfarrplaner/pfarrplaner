<?php
/*
 * dienstplan
 *
 * Copyright (c) 2019 Christoph Fischer, https://christoph-fischer.org
 * Author: Christoph Fischer, chris@toph.de
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

namespace App\Reports;

use Illuminate\Support\Facades\Auth;
use PDF;

class AbstractPDFDocumentReport extends AbstractReport
{
    public $icon = 'fa fa-file-pdf';

    public function sendToBrowser($filename, $data, $layout) {
        $pdf = PDF::loadView($this->getRenderViewName(), $data, [], array_merge([
            'author' => isset(Auth::user()->name) ? Auth::user()->name : Auth::user()->email,
        ], $layout));
        return $pdf->stream($filename);

    }
}
