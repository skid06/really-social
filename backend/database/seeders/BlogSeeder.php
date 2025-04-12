<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default user if none exists
        if (User::count() === 0) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password')
            ]);
        }

        // Create 25 random blogs
        Blog::factory()->count(25)->create();

        // Create 10 published blogs
        Blog::factory()->count(10)->published()->create();

        // Create 5 hidden blogs
        Blog::factory()->count(5)->hidden()->create();
    }
}
