<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Blog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'content' => $this->faker->paragraphs(rand(3, 7), true),
            'status' => $this->faker->randomElement(['published', 'hidden']),
            'created_by' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fn (array $attributes) => $attributes['created_at'],
        ];
    }

    /**
     * Indicate that the blog is published.
     *
     * @return Factory
     */
    public function published(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'published',
            ];
        });
    }

    /**
     * Indicate that the blog is hidden.
     *
     * @return Factory
     */
    public function hidden(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'hidden',
            ];
        });
    }
}
