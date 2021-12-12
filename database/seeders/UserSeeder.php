<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * The password for all pre-registered acount is -> 12345678
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            "name" => "Kis Kacsa",
            "email" => "kiskacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('users')->insert([
            "name" => "Nagy Kacsa",
            "email" => "nagykacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('users')->insert([
            "name" => "Mama Kacsa",
            "email" => "mamakacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            "name" => "Papa Kacsa",
            "email" => "papakacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
    }
}