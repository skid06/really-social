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
        // Create 10 users
        $users = User::factory()->count(10)->create();

        // Helper to assign a random user
        $assignUser = function ($blog) use ($users) {
            $blog->created_by = $users->random()->id;
            $blog->save();
        };

        // Create 25 general blogs
        Blog::factory()
            ->count(25)
            ->create()
            ->each($assignUser);

        // Create 10 published blogs
        Blog::factory()
            ->count(10)
            ->published()
            ->create()
            ->each($assignUser);

        // Create 5 hidden blogs
        Blog::factory()
            ->count(5)
            ->hidden()
            ->create()
            ->each($assignUser);
    }
}
