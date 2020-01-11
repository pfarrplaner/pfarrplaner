<?php


namespace App\Traits;


use App\Attachment;

trait HasAttachmentsTrait
{

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

}
