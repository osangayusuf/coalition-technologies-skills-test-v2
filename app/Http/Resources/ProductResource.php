<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'quantity' => (int) $this['quantity'],
            'price' => (float) $this['price'],
            'created_at' => $this['created_at'],
            'total_value' => (float) $this['quantity'] * (float) $this['price'],
        ];
    }
}
