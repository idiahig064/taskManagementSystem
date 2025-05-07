<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->text(50), // Genera un título con una longitud máxima de 50 caracteres
            'description' => $this->faker->optional()->text(500), // Genera una descripción opcional con una longitud máxima de 500 caracteres
            'due_date' => $this->faker->date(),
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High', 'Urgent']),
            'status' => $this->faker->randomElement(['completed', 'incomplete']),
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
