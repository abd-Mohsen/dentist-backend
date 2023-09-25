<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'title' => 'cat1',
            'image_id' => 3,
            'parent_id' => null,
        ]);
        Category::create([
            'title' => 'cat2',
            'image_id' => 4,
            'parent_id' => null,
        ]);
        Category::create([
            'title' => 'cat3',
            'image_id' => 5,
            'parent_id' => 1,
        ]);
        
    }
}
