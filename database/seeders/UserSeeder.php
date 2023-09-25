<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'user1',
            'email' => 'a1@m.co',
            'password' => bcrypt('12345678'),
            'phone' => '6969',
            'role_id' => 1,
            'image_id' => null,
        ]);
        User::create([
            'name' => 'user2',
            'email' => 'a2@m.co',
            'password' => bcrypt('12345678'),
            'phone' => '6969',
            'role_id' => 2,
            'image_id' => 6,
        ]);
        User::create([
            'name' => 'user3',
            'email' => 'a3@m.co',
            'password' => bcrypt('12345678'),
            'phone' => '6969',
            'role_id' => 3,
            'image_id' => null,
        ]);
    }
}
