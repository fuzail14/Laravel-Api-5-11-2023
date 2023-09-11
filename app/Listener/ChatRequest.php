<?php

namespace App\Listener;

use App\Event\ChatEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use PhpParser\Node\NullableType;

class ChatRequest
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

     /**
     * Handle the event.
     *
     * @param  \App\Event\ChatEvent  $events
     * @return void
     */
    public function handle(ChatEvent $d)
    {

         return $d;
    }
}
