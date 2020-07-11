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

namespace App\Traits;


use App\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Trait HandlesAttachmentsTrait
 * @package App\Traits
 */
trait HandlesAttachmentsTrait
{

    /**
     * @param Request $request
     * @param $object
     */
    protected function handleAttachments(Request $request, $object)
    {
        if ($request->hasFile('attachments')) {
            $files = $request->file('attachments');
            foreach ($files as $key => $file) {
                $path = $file->store('attachments');
                $description = $request->get('attachment_text')[$key] ?: pathinfo(
                    $file->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $object->attachments()->create(['title' => $description, 'file' => $path]);
            }
        }

        $this->removeAttachments($request, $object);
    }

    /**
     * @param Request $request
     * @param $object
     */
    protected function removeAttachments(Request $request, $object)
    {
        if ($request->has('remove_attachment')) {
            foreach ($request->get('remove_attachment') as $attachmentId) {
                $attachment = Attachment::findOrFail($attachmentId);
                Storage::delete($attachment->file);
                $object->attachments()->where('id', $attachmentId)->delete();
                $attachment->delete();
            }
        }
    }

    /**
     * @param Request $request
     * @param $object
     * @param $key
     */
    protected function handleIndividualAttachment(Request $request, $object, $key)
    {
        if ($request->hasFile($key)) {
            // delete previous upload
            if ($object->$key != '') {
                Storage::delete($object->$key);
            }
            $file = $request->file($key);
            $path = $file->store('attachments');
            $object->update([$key => $path]);
        }

        $this->removeIndividualAttachment($request, $object, $key);
    }

    /**
     * @param Request $request
     * @param $object
     * @param $key
     */
    protected function removeIndividualAttachment(Request $request, $object, $key)
    {
        if ($request->has('remove_' . $key)) {
            if (Storage::exists($object->$key)) {
                Storage::delete($object->$key);
            }
            $object->update([$key => '']);
        }
    }

}
