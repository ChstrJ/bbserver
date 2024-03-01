<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    protected $message;

    public function __construct($resource,  $message = null)
    {
        parent::__construct($resource);
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            "product" => [
                "id" => $this->id,
                "categoryId" => $this->category_id,
                "name" => $this->name,
                "description" => $this->description,
                "quantity" => $this->quantity,
                "srp" => $this->srp,
                "memberPrice" => $this->member_price,
                "isRemove" => $this->is_remove,
                "userId" => $this->user_id,
                "createdAt" => $this->created_at,
                "updatedAt" => $this->updated_at,
            ],
            "message" => $this->message,
        ];
    }
}
