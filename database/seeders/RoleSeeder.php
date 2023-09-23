<?php

namespace Database\Seeders;
 
use App\Models\Role;
use Illuminate\Database\Seeder;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['title' => 'admin']);
        Role::create(['title' => 'dentist']);
        Role::create(['title' => 'supplier']);
    }
}
