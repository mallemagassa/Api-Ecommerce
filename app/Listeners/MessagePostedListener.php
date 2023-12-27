<?php

namespace App\Listeners;

use Illuminate\Bus\Queueable;
use App\Events\MessageWasPosted;
use App\Jobs\ProcessPendingMessages;
use Illuminate\Support\Facades\Queue;


class MessagePostedListener
{
    
    /**
     * Execute the job.
     */

    public function handle(MessageWasPosted $event)
    {
        $message = $event->message;

        if ($message->conversation->receiver->statusOn == 'online') {
            Queue::push(new ProcessPendingMessages($message));
        }
    }
}
