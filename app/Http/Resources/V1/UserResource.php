<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $accessToken;
    protected $message;

    public function __construct($resource, $accessToken = null, $message = null)
    {
        parent::__construct($resource);
        $this->accessToken = $accessToken;
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
            'user_id' => $this->id,
            'full_name' => $this->full_name,
            'username' => $this->username,
            'is_active' => $this->is_active,
            'products_added' => ProductResource::collection($this->whenLoaded('products')),
            'transactions_added' => TransactionResource::collection($this->whenLoaded('transactions')),
        ];
    }
}
