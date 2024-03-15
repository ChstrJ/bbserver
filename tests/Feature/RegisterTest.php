<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example()
    {


       //preperation / prepare

       //action / perform
        $response = $this->getJson('products');
        
       //assertion / predict
       $this->assertEquals(5, count($response->json()));
    }


}
