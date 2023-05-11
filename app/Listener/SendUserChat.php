<?php

namespace App\Listener;

use App\Event\UserChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendUserChat
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
     * @param  \App\Event\UserChat  $event
     * @return void
     */
    public function handle(UserChat $event)
    {


        return $event;

    }
}
