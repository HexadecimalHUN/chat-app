<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            "name" => "Kiskacsa",
            "email" => "kiskacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('users')->insert([
            "name" => "Nagykacsa",
            "email" => "nagykacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        
        DB::table('users')->insert([
            "name" => "Mamakacsa",
            "email" => "mamakacsa@gmail.com",
            "password" => '$2y$10$MEXgPP8QCYtWwkgK47JaRuhHGBcTGj4lahQXLhPQTDt5.TlMALUDm',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'last_seen' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            "name" => "Papakacsa",
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