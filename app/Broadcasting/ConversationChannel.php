<?php

namespace App\Broadcasting;

use App\Models\Conversation;
use App\Models\User;;

class ConversationChannel
{
    /**
     * Authenticate the user's access to the channel.
     *
     * @param User         $user
     * @param Conversation $conversation
     *
     * @return array|bool
     */
    public function join(User $user, Conversation $conversationId)
    {
       // $users = collect([$conversation->sender, $conversation->receiver]);
       $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return false;
        }
        return ($user->id === $conversation[0]->sender->id || $user->id === $conversation[0]->receiver->id);
    }
}