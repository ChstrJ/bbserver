<?php

namespace App\Http\Utils;
use Exception;

class ErrorMessage {
    public function notFound(){
        throw new Exception("The requested resource is not found");
    }
}