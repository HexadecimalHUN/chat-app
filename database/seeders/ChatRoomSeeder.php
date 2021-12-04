<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('chat_rooms')->insert([
            "chat_room_id" => '61aacf05181cf',
            "user_id" => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('chat_rooms')->insert([
            "chat_room_id" => '61aacf05181cf',
            "user_id" => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}