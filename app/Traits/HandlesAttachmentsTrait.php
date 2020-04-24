<?php


namespace App\Traits;


use App\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesAttachmentsTrait
{

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


    protected function removeIndividualAttachment(Request $request, $object, $key)
    {
        if ($request->has('remove_'.$key)) {
            if (Storage::exists($object->$key)) {
                Storage::delete($object->$key);
            }
            $object->update([$key => '']);
        }
    }

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

}
