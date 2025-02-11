<?php

namespace App\Http\Resources;

use App\Models\ProductLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'logs' => ProductLogResource::collection($this->logs->sortByDesc('created_at')),
            'inventory_logs' => ProductInventoryLogResource::collection($this->inventoryLogs->sortByDesc('created_at')),
        ]);
    }
}
