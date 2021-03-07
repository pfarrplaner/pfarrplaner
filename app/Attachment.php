<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
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

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

/**
 * Class Attachment
 * @package App
 */
class Attachment extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = ['title', 'file', 'attachable'];

    protected $appends = ['size', 'mimeType', 'icon', 'extension'];

    /**
     * @return MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    public function getSizeAttribute()
    {
        return Storage::size($this->file);
    }

    public function getMimeTypeAttribute()
    {
        return Storage::mimeType($this->file);
    }

    /**
     * Get correct file icon by mime type
     * @return string
     * @source https://gist.github.com/colemanw/9c9a12aae16a4bfe2678de86b661d922
     */
    public function getIconAttribute()
    {
        $icon_classes = [
            // Media
            'image' => 'fa-file-image',
            'audio' => 'fa-file-audio',
            'video' => 'fa-file-video',
            // Documents
            'application/pdf' => 'fa-file-pdf',
            'application/msword' => 'fa-file-word',
            'application/vnd.ms-word' => 'fa-file-word',
            'application/vnd.oasis.opendocument.text' => 'fa-file-word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml' => 'fa-file-word',
            'application/vnd.ms-excel' => 'fa-file-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml' => 'fa-file-excel',
            'application/vnd.oasis.opendocument.spreadsheet' => 'fa-file-excel',
            'application/vnd.ms-powerpoint' => 'fa-file-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml' => 'fa-file-powerpoint',
            'application/vnd.oasis.opendocument.presentation' => 'fa-file-powerpoint',
            'text/plain' => 'fa-file-text',
            'text/html' => 'fa-file-code',
            'application/json' => 'fa-file-code',
            // Archives
            'application/gzip' => 'fa-file-archive',
            'application/zip' => 'fa-file-archive',
        ];
        foreach ($icon_classes as $text => $icon) {
            if (strpos($this->mimeType, $text) === 0) {
                return $icon;
            }
        }
        return 'fa-file';
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->file, PATHINFO_EXTENSION);
    }

}
