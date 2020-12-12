<?php

namespace App\Listeners;

use App\Events\SendEmail;
use App\Mail\PartnerAccountEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailFired
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SendEmail  $event
     * @return void
     */
    public function handle(SendEmail $event)
    {
        //
       Mail::to(trim($event->recipient))->send(new PartnerAccountEmail($event->content));
    }
}
