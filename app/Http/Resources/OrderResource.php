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
            'id' => $request->id,
            'customer' => $request->customer,
            'discount'=> $request->discount,
            'price_on_created' => $request->price_on_created,
            'purchase_price_on_created' => $request->purchase_price_on_created,
            'created_at' => $request->created_at,
            'updated_at' => $request->updated_at,
        ];
    }
}
