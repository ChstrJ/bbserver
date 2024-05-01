<?php

namespace App\Http\Helpers\product;

use App\Models\Product;


class ProductService
{
    public static function generateProductCode()
    {
        $product = Product::latest('created_at')->first();
        if(!$product) {
            return 'BB' . 1;
        }
        
        $temp =  'BB' . $product->id;

        if($product->product_code === $temp) {
            return 'BB' . $product->id + 1;
        }
        
        return $temp;
    }
}