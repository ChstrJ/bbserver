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
            'transaction' => [
                "id" => $this->id,
                "amountDue" => $this->amount_due,
                "numberOfItems" => $this->number_of_items,
                "paymentType" => $this->payment_type,
                "createdAt" => $this->created_at,
                "updatedAt" => $this->updated_at,
                // "user" => [
                //     "user_id" => $this->user_id,
                //     "username" => $this->username
                // ],
                // "products" => [
                //     "Capucino" => [
                //         "qty" => "2",
                //         "price" => "232",
                //     ],
                //     "Decafe" => [
                //         "qty" => "5",
                //         "price" => "232",
                //     ],
                //     "Kapeng Barako" => [
                //         "qty" => "5",
                //         "price" => "232",
                //     ],
                //     "total" => "23123",
                // ]
            ],
            'message' => $this->message,
        ];
    }
}
