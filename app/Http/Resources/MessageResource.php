<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'type' => $this->type,
            'text' => $this->text,
            'media' => $this->media,
            'video' => $this->video,
            'document' => $this->document,
            'is_sender_delete' => (bool) $this->is_sender_delete,
            'is_receiver_delete' => (bool) $this->is_receiver_delete,
            'conversation_id' => $this->conversation_id,
        ];
    }
}
