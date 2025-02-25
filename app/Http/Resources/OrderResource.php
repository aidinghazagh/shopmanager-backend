<?php

namespace App\Http\Resources;

use App\Models\OrderProduct;
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'order_products' => OrderProductResource::collection($this->orderProducts),
        ];
    }
}
