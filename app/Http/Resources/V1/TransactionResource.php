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
            "amount_due" => $this->amount_due,
            "number_of_items" => $this->number_of_items,
            "payment_method" => $this->payment_method,
            "checkouts" => $this->checkouts,
            "status" => $this->status,
            "transacted_by" => $this->user->username,
            "customer_id" => $this->customer_id,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
