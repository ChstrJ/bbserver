<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    protected $token;
    protected $message;
    
    public function __construct($resource, $token = null, $message = null)
    {
        parent::__construct($resource);
        $this->token = $token;
        $this->message = $message;
    }
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        // $data = [
        //     'user' => [
        //         'id' => $this->id,
        //         'fullName' => $this->full_name,
        //         'username' => $this->username,
        //         'isActive' => $this->is_active,
        //     ],
        //     'token' => $this->token,
        //     'message' => $this->message,
        // ];
        // return $data;

        return [
            'id' => $this->id,
            'fullName' => $this->full_name,
            'username' => $this->username,
            'isActive' => $this->is_active,
        ];
    }
}
