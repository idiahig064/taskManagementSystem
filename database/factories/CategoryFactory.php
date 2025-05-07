<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        // Obtener el número actual de categorías en la base de datos
        $currentCount = Category::count();

        // Obtener un padre aleatorio o establecerlo como null
        $parentCategory = Category::inRandomOrder()->first();
        $parentId = $parentCategory ? $parentCategory->id : null;

        return [
            'name' => $this->faker->name(),
            'color_code' => $this->faker->hexColor(),
            'description' => $this->faker->text(),
            'parent_id' => $parentId,
            'position' => $currentCount + 1, // Asignar posición basada en el conteo actual
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
