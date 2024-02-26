<?php

namespace App\Http\Helpers;

class GenericMessage {
    public static function productAdded(string $product_name)
    {
        return "{$product_name} successfully added to inventory.";
    }
    public static function transactionAdded(string $username)
    {
        return "{$username}'s transaction successfully added.";
    }
  }