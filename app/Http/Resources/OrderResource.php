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
            'customer' => $this->customer,
            'discount'=> $this->discount,
            'price_on_created' => $this->price_on_created,
            'purchase_price_on_created' => $this->purchase_price_on_created,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products' => ProductResource::collection($this->products),
        ];
    }
}
