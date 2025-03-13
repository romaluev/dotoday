<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'is_completed' => fake()->boolean(20),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+30 days'),
            'priority' => fake()->randomElement(TaskPriority::cases())->value,
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the task is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }

    /**
     * Indicate that the task is not completed.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => false,
        ]);
    }

    /**
     * Set the task priority.
     */
    public function priority(TaskPriority|string $priority): static
    {
        $priorityValue = $priority instanceof TaskPriority ? $priority->value : $priority;
        
        return $this->state(fn (array $attributes) => [
            'priority' => $priorityValue,
        ]);
    }

    /**
     * Set the due date for the task.
     */
    public function dueDate(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $date,
        ]);
    }
}
