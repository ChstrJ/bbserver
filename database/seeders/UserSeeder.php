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
       DB::table('users')->insert([
        [
            "full_name" => "Chester Jazmin",
            "username" => "admin1",
            "password" => bcrypt('1234'),
            "role_id" => 1,
        ],
        [
            "full_name" => "Keanno Regino",
            "username" => "admin2",
            "password" => bcrypt('1234'),
            "role_id" => 1,
        ],
        [
            "full_name" => "Jeline Cadayday",
            "username" => "employee1",
            "password" => bcrypt('1234'),
            "role_id" => 2,
        ]
       ]);
    }
}
