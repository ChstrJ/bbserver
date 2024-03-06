<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{

    protected $message;

    public function __construct($resource, $message = null)
    {
        parent::__construct($resource);
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            "id" => $this->id,
            "amount_due" => $this->amount_due,
            "number_of_items" => $this->number_of_items,
            "payment_type" => $this->payment_type,
            "created_by" => $this->user->username,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
