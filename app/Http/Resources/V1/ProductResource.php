<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "category_id" => $this->category_id,
            "name" => $this->name,
            "description" => $this->description,
            "quantity" => $this->quantity,
            "srp" => $this->srp,
            "member_price" => $this->member_price,
            "added_by" => $this->user->username,
            "is_removed" => $this->is_removed,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,   
        ];
    }
}
