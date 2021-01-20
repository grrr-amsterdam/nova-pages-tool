<?php

namespace Grrr\Pages\Database\Factories;

use Grrr\Pages\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'status' => Page::STATUS_PUBLISHED,
            'language' => config('app.locale'),
        ];
    }
}
