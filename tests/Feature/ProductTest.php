<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductTest extends TestCase
{


   
    /**
     * A basic feature test example.
     */
    // public function test_api_return_products() {

    //     $product = Product::factory()->create();

    //     $response = $this->getJson('/api/v1/products');

    //     $response->assertJson($product->toArray());
    // }

    public function test_api_store_successfull() {

        $product = [ 
            'category_id' => 1,
            'name' => 1,
            'description' => 1,
            'quantity' => 1,
            'srp' => 1,
            'member_price' => 1,
            'user_id' => 1,
        ];

        $response = $this->postJson('http://127.0.0.1:8000/api/v1/products/', $product);

        $response->assertStatus(200);
        $response->assertJson($product);

        
    }
}

