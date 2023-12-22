<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'numOrder' => $this->numOrder,
            'priceTotal' => $this->priceTotal,
            'quantity' => $this->quantity,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
        ];
    }
}
