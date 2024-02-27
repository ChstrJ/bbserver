<?php

namespace App\Http\Helpers;

class ProductCategories
{
    public static function getCategories()
    {
        $categories = [
            [
                "id" => 1,
                "name" => "Beverages"
            ],
            [
                "id" => 2,
                "name" => "Powder"
            ],
            [
                "id" => 3,
                "name" => "Dairy"
            ],
            [
                "id" => 4,
                "name" => "Goods"
            ]
        ];
        $categories_length = count($categories);
        return $categories_length;
    }
}
