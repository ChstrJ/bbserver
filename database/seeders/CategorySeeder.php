<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')
        ->insert([
            [
                "name" => "Coffee Grounds",
                "created_at" => now()
            ],
            [
                "name" => "Syrup",
                "created_at" => now()
            ],
            [
                "name" => "Equipment",
                "created_at" => now()
            ],
            [
                "name" => "Category1",
                "created_at" => now()
            ],
            [
                "name" => "Category2",
                "created_at" => now()
            ],
            [
                "name" => "Category3",
                "created_at" => now()
            ],
            [
                "name" => "Category4",
                "created_at" => now()
            ],
            [
                "name" => "Category5",
                "created_at" => now()
            ],
        ]);
    }
}
