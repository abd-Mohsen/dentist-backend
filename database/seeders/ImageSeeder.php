<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Image::create([
            'path' => 'storage/brand/brand1.jpg',
            'type' => 'brand',
        ]);

        Image::create([
            'path' => 'storage/brand/brand2.jpg',
            'type' => 'brand',
        ]);

        Image::create([
            'path' => 'storage/category/category1.jpg',
            'type' => 'category',
        ]);

        Image::create([
            'path' => 'storage/category/category2.jpg',
            'type' => 'category',
        ]);

        Image::create([
            'path' => 'storage/category/category3.jpg',
            'type' => 'category',
        ]);

        Image::create([
            'path' => 'storage/profile/profile1.jpg',
            'type' => 'profile',
        ]);
    }
}
