<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder03Mar26 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'image' => 'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'image' => 'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen', 'image' => 'https://images.unsplash.com/photo-1616046229478-9901c5536a45?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-outdoors', 'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Books', 'slug' => 'books', 'image' => 'https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Beauty & Personal Care', 'slug' => 'beauty', 'image' => 'https://images.unsplash.com/photo-1612817288484-6f916006741a?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Toys & Games', 'slug' => 'toys-games', 'image' => 'https://images.unsplash.com/photo-1596461404969-9ae70f2830c1?w=600&h=400&fit=crop&q=80'],
            ['name' => 'Automotive', 'slug' => 'automotive', 'image' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=600&h=400&fit=crop&q=80'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
