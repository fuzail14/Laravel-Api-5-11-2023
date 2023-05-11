<?php

namespace App\Listener;

use App\Event\DiscussionChatEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDiscussionChat
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
     * @param  \App\Event\DiscussionChatEvent  $event
     * @return void
     */
    public function handle(DiscussionChatEvent $event)
    {
      

        return $event;
    }
}
