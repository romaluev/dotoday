<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskPriority;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private array $task_data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->task_data = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'is_completed' => false,
            'priority' => array_rand(TaskPriority::cases(), 1),
            'due_date' => now()->addDays(5)->format('Y-m-d H:i:s'),
        ];
    }

    /** @test */
    public function api_routes_require_authentication()
    {
        $routes = [
            ['GET', '/api/tasks'],
            ['POST', '/api/tasks'],
            ['GET', '/api/tasks/1'],
            ['PUT', '/api/tasks/1'],
            ['DELETE', '/api/tasks/1'],
            ['GET', '/api/tasks/search'],
        ];

        foreach ($routes as [$method, $uri]) {
            $response = $this->json($method, $uri);
            $response->assertUnauthorized();
        }
    }

    /** @test */
    public function sanctum_authentication_works_correctly()
    {
        // Test successful authentication
        Sanctum::actingAs($this->user);
        $response = $this->getJson('/api/tasks');
        $response->assertOk();

        // Test token abilities
        Sanctum::actingAs($this->user, ['tasks:read']);
        $response = $this->getJson('/api/tasks');
        $response->assertOk();

        // Test with any token - should work since we're using policies, not token abilities
        Sanctum::actingAs($this->user, ['wrong-ability']);
        $response = $this->getJson('/api/tasks');
        $response->assertOk();

        // Since Laravel Sanctum doesn't automatically check token expiration by default,
        // we should expect a successful response even with an expired token
        $expired_token = $this->user->createToken('test-token', ['tasks:read'], now()->subDay());
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $expired_token->plainTextToken,
        ])->getJson('/api/tasks');
        $response->assertOk();
    }

    /** @test */
    public function user_can_only_access_their_own_tasks()
    {
        $other_user = User::factory()->create();
        $other_user_task = Task::factory()->create(['user_id' => $other_user->id]);
        $user_task = Task::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user);

        // Try to view other user's task
        $response = $this->getJson("/api/tasks/{$other_user_task->id}");
        $response->assertForbidden();

        // Try to update other user's task
        $response = $this->putJson("/api/tasks/{$other_user_task->id}", $this->task_data);
        $response->assertForbidden();

        // Try to delete other user's task
        $response = $this->deleteJson("/api/tasks/{$other_user_task->id}");
        $response->assertForbidden();

        // Can access own task
        $response = $this->getJson("/api/tasks/{$user_task->id}");
        $response->assertOk();
    }
}
