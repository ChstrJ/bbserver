<?php

namespace App\Http\Utils;

class DynamicMessage {
    public static function productAdded(string $product_name)
    {
        return "{$product_name} successfully added to inventory.";
    }
    public static function productUpdated(string $product_name)
    {
        return "{$product_name} successfully updated to inventory.";
    }

    public static function productRemove(string $product_name)
    {
        return "{$product_name} successfully removed to inventory.";
    }
    public static function transactionAdded(string $username)
    {
        return "{$username}'s transaction successfully added.";
    }

    public static function customerAdded(string $customer)
    {
        return "{$customer}'s details successfully added.";
    }

    public static function customerUpdated(string $customer)
    {
        return "{$customer}'s details successfully updated.";
    }

    public static function customerRemove(string $customer)
    {
        return "{$customer}'s successfully removed.";
    }
  }
