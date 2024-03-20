<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'is_active' => $this->is_active,
            'products_added' => ProductResource::collection($this->whenLoaded('products')),
            'transactions_added' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
