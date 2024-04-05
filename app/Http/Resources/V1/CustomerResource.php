<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'customer_id' => $this->id,
            'name' => $this->name,
            'phone_number' => $this->phone_number,
            'transactions' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
