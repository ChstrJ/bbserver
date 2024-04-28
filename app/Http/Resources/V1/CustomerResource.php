<?php

namespace App\Http\Resources\V1;

use App\Http\Helpers\user\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "phone_number" => $this->phone_number,
            "email_address" => $this->email_address,
            "address" => $this->address,
            "created_at" => $this->created_at->format('m-d-Y'),
            "updated_at" => $this->updated_at->format('m-d-Y'),
            "created_by" => $this->created_by ? UserService::getUsernameById($this->created_by) : null,
            "updated_by" => $this->updated_by ? UserService::getUsernameById($this->updated_by) : null,
            "transactions" => TransactionResource::collection($this->whenLoaded("transactions")),
        ];
    }
}
