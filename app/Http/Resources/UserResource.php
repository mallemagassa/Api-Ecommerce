<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "phone" => $this->phone,
            "nameCom" => $this->nameCom,
            "status" => $this->status,
            "address" => $this->address,
            "isSeller" => (bool) $this->isSeller,
            "conversation_id" => $this->conversation_id,
            "receiver_id" => $this->receiver_id,
        ];
    }
}
