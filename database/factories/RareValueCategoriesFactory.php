<?php

namespace Database\Factories;

use App\Models\WebsiteRareValueCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class RareValueCategoriesFactory extends Factory
{
    protected $model = WebsiteRareValueCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
