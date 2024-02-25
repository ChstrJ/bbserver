<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $number_of_products = 15;
        Product::factory()->count($number_of_products)->create();
    }
}
