<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class   OrderProductResource extends JsonResource
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
            'quantity' => $this->quantity,
            'name_on_created' => $this->name_on_created,
            'price_on_created' => $this->price_on_created,
            'purchase_price_on_created' => $this->purchase_price_on_created,
        ];
    }
}
