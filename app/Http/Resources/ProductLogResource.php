<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'changed_field' => $this->changed_field,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'created_at' => $this->created_at,
        ];
    }
}
