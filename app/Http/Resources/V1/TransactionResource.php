<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request)


    {
        return [
                "id" => $this->id,
                "reference_number" => $this->reference_number,
                "amount_due" => $this->amount_due,
                "number_of_items" => $this->number_of_items,
                "payment_method" => $this->payment_method,
                "checkouts" => $this->checkouts,
                "status" => $this->status,
                "commission" => floatval($this->commission),
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at,
                "employee" => new UserResource($this->whenLoaded('user')),
                "customer" => new CustomerResource($this->whenLoaded('customer')),
        ];
    }
}
