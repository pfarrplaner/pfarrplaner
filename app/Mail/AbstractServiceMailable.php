<?php
/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 17.05.2019
 * Time: 16:51
 */

namespace App\Mail;


use App\Service;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractServiceMailable extends Mailable
{

    use Queueable, SerializesModels;

    /** @var Service $service */
    protected $service;

    /** @var array $data */
    protected $data = [];

    /** @var User $user */
    protected $user;

    /** @var Service $original */
    protected $original;

    protected $changed = [];

    public function __construct(User $user, Service $service, array $data)
    {
        $this->user = $user;
        $this->service = $service;
        $this->original =$service->originalObject;
        $this->changed = $service->changedFields;
        $this->data = $data;
    }

}
