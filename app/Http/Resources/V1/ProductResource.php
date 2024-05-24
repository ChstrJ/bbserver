<?php

namespace App\Http\Resources\V1;

use App\Http\Helpers\user\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "product_code" => $this->product_code,
            "category_id" => $this->category_id,
            "name" => $this->name,
            "description" => $this->description,
            "quantity" => $this->quantity,
            "srp" => $this->srp,
            "member_price" => $this->member_price,
            "is_removed" => $this->is_removed,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "created_by" => $this->created_by ? UserService::getFullnameById($this->created_by) : null,
            "updated_by" => $this->updated_by ? UserService::getFullnameById($this->updated_by) : null,
        ];
    }
}
