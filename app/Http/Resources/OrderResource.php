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
            'imageUrl' => $this->imageUrl,
            'product_name' => $this->product_name,
            'product_price' => $this->product_price,
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'count' => $this->count,
            'created_at' => $this->created_at,
        ];
    }
}
