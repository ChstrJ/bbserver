<?php

namespace App\Http\Helpers;

class DynamicMessage {
    public static function productAdded(string $product_name)
    {
        return "{$product_name} successfully added to inventory.";
    }
    public static function productUpdated(string $product_name)
    {
        return "{$product_name} successfully updated to inventory.";
    }
    public static function transactionAdded(string $username)
    {
        return "{$username}'s transaction successfully added.";
    }
  }

class GenericMessage {
    public static $TRANSACT = "Transaction successfully added";
}