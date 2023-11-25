<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'supplier' => new UserResource($this->supplier),
            'status' => $this->status, 
            'ordered_products' => ProductOrderResource::collection($this->products),
            //'date' => $this->created_at, // wont need it cuz its included in the OG order
            //'customer' => $this->order->customer, //eager load
        ];
    }
}
