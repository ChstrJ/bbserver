<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "full_name" => $this->full_name,
            "description" => $this->description,
            "start_time" => $this->start_time,
            "end_time" => $this->end_time,
            "date" => $this->date, //para sa calendar
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "customer" => new CustomerResource($this->whenLoaded('customer')),
            "employee" => new UserResource($this->whenLoaded('user'))
        ];
    }
}
