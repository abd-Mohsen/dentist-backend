<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::create([
            'title' => 'some brand',
            'image_id' => 1,
        ]);
        Brand::create([
            'title' => 'another brand',
            'image_id' => 2,
        ]);
    }
}
