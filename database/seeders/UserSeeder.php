<?php

namespace Database\Seeders;

use App\Http\Utils\Role;
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
            "full_name" => "Super admin",
            "username" => "super1",
            "password" => bcrypt('1234'),
            "role_id" => Role::SUPER_ADMIN,
            "created_at" => now()
        ],
        [
            "full_name" => "Chester Jazmin",
            "username" => "admin1",
            "password" => bcrypt('1234'),
            "role_id" => Role::ADMIN,
            "created_at" => now()
        ],
        [
            "full_name" => "Keanno Regino",
            "username" => "admin2",
            "password" => bcrypt('1234'),
            "role_id" => Role::ADMIN,
            "created_at" => now()
        ],
        [
            "full_name" => "Jerome Cruz Ilunio",
            "username" => "admin3",
            "password" => bcrypt('1234'),
            "role_id" => Role::ADMIN,
            "created_at" => now()
        ],
        [
            "full_name" => "Jeline Cadayday",
            "username" => "employee1",
            "password" => bcrypt('1234'),
            "role_id" => Role::EMPLOYEE,
            "created_at" => now()
        ]
       ]);
    }
}
