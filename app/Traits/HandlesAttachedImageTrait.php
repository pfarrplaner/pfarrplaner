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

namespace App\Traits;

use App\Sermon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesAttachedImageTrait
{

    public function attachImage(Request $request, $model, $field = null)
    {

        $modelObject = $this->model::find($model);
        $imageField = $field ?? $modelObject->getImageField();
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach($files as $file) {
                $fullPath = $file->store('attachments');
                $modelObject->update([$imageField => $fullPath]);
                $path = basename($fullPath);
                return response()->json(['url' => route('image', compact('path')), 'image' => $fullPath,
                                            'model' => $modelObject, 'id' => $model, 'field' => $imageField]);
            }
        } elseif ($request->has('uploadFromUrl')) {
            $fullPath = 'attachments/'.Str::random(32).'.'.pathinfo($request->get('uploadFromUrl'), PATHINFO_EXTENSION);
            Storage::put($fullPath, file_get_contents($request->get('uploadFromUrl')));
            $modelObject->update([$imageField => $fullPath]);
            $path = basename($fullPath);
            return response()->json(['url' => route('image', compact('path')), 'image' => $fullPath,
                                        'model' => $modelObject, 'id' => $model, 'field' => $imageField]);
        }

    }

    public function detachImage(Request $request, $model, $field = null)
    {
        $modelObject = $this->model::find($model);
        $imageField = $field ?? $modelObject->getImageField();
        if ($modelObject->$imageField) {
            Storage::delete($modelObject->$imageField);
            $modelObject->update([$imageField => '']);
        }
        return response()->json();
    }
}
