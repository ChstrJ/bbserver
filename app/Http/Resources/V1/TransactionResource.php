<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request)

    //strtoupper($this->payment_type)
    {
        return [
            "id" => $this->id,
            "amount_due" => number_format($this->amount_due, 2),
            "number_of_items" => $this->number_of_items,
            "payment_method" => $this->payment_method,
            "checkouts" => $this->checkouts,
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "employee" => new UserResource($this->whenLoaded('user')),
            "customer" => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
