<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       User::factory()->count(7)->create();
       DB::table('users')->insert([
        [
            "full_name" => "Chester Jazmin",
            "username" => "admin1",
            "password" => bcrypt('1234'),
            "role_id" => 1,
            "created_at" => now()
        ],
        [
            "full_name" => "Keanno Regino",
            "username" => "admin2",
            "password" => bcrypt('1234'),
            "role_id" => 1,
            "created_at" => now()
        ],
        [
            "full_name" => "Jeline Cadayday",
            "username" => "employee1",
            "password" => bcrypt('1234'),
            "role_id" => 2,
            "created_at" => now()
        ]
       ]);
    }
}
