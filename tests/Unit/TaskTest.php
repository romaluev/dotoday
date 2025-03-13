<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Enums\TaskPriority;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Task $task;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->task = Task::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'is_completed' => false,
            'priority' => TaskPriority::MEDIUM->value,
            'due_date' => now()->addDays(5),
        ]);
    }

    /** @test */
    public function task_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->task->author);
        $this->assertEquals($this->user->id, $this->task->author->id);
        $this->assertEquals($this->user->name, $this->task->author->name);
    }

    /** @test */
    public function task_attributes_are_properly_cast()
    {
        $this->assertIsString($this->task->title);
        $this->assertIsString($this->task->description);
        $this->assertIsBool($this->task->is_completed);
        $this->assertIsString($this->task->priority->value);
        $this->assertInstanceOf(Carbon::class, $this->task->due_date);
        $this->assertInstanceOf(Carbon::class, $this->task->created_at);
        $this->assertInstanceOf(Carbon::class, $this->task->updated_at);
    }

    /** @test */
    public function task_has_correct_fillable_attributes()
    {
        $expected = [
            'user_id',
            'title',
            'description',
            'is_completed',
            'due_date',
            'priority'
        ];

        $this->assertEquals($expected, $this->task->getFillable());
    }

    /** @test */
    public function task_status_scope_filters_correctly()
    {
        Task::query()->forceDelete();

        // Create tasks with different statuses
        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => TaskPriority::LOW->value,
            'is_completed' => true
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'priority' => TaskPriority::MEDIUM->value,
            'is_completed' => false
        ]);

        // Test each statuses
        $todo_tasks = Task::notCompleted()->get();
        $completed_tasks = Task::completed()->get();

        $this->assertEquals(1, $todo_tasks->count());
        $this->assertEquals(1, $completed_tasks->count());
    }

    /** @test */
    public function task_priority_scope_filters_correctly()
    {
        Task::query()->forceDelete();

        // Create tasks with different priorities
        foreach (TaskPriority::cases() as $priority) {
            Task::factory()->create([
                'user_id' => $this->user->id,
                'priority' => $priority->value
            ]);
        }

        // Test each priority lvl
        foreach (TaskPriority::cases() as $priority) {
            $tasks = Task::priority($priority->value)->get();
            $this->assertEquals(
                1,
                $tasks->count(),
                "Expected 1 task with priority {$priority->value}"
            );
            $this->assertEquals($priority->value, $tasks->first()->priority->value);
        }
    }

    /** @test */
    public function task_due_date_scope_filters_correctly()
    {
        Task::query()->forceDelete();

        $dates = [
            now()->subDay(),
            now(),
            now()->addDay(),
            now()->addDays(5),
            now()->addWeek(),
        ];

        // Create tasks with different due dates
        foreach ($dates as $date) {
            Task::factory()->create([
                'user_id' => $this->user->id,
                'due_date' => $date
            ]);
        }

        // Test filter by specific date
        foreach ($dates as $date) {
            $formated_date = $date->format('Y-m-d');
            $tasks = Task::dueDate($formated_date)->get();

            $this->assertEquals(1, $tasks->count());
            $this->assertEquals(
                $formated_date,
                $tasks->first()->due_date->format('Y-m-d')
            );
        }
    }

    /** @test */
    public function task_search_array_includes_required_fields()
    {
        $search_array = $this->task->toSearchableArray();

        $required_fieleds = [
            'id',
            'user_id',
            'title',
            'description',
            'is_completed',
            'priority',
            'due_date',
            'created_at',
            'updated_at'
        ];

        foreach ($required_fieleds as $field) {
            $this->assertArrayHasKey($field, $search_array);
        }

        // Verify the values are correct
        $this->assertEquals($this->task->id, $search_array['id']);
        $this->assertEquals($this->task->user_id, $search_array['user_id']);
        $this->assertEquals($this->task->title, $search_array['title']);
        $this->assertEquals($this->task->description, $search_array['description']);
        $this->assertEquals($this->task->is_completed, $search_array['is_completed']);
        $this->assertEquals($this->task->priority->value, $search_array['priority']);
    }

    /** @test */
    public function task_scope_for_user_filters_correctly()
    {
        // Create tasks for different users
        $other_user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $other_user->id]);
        Task::factory()->count(2)->create(['user_id' => $this->user->id]);

        $user_tasks = Task::forUser($this->user->id)->get();
        $other_user_tasks = Task::forUser($other_user->id)->get();

        // Including the task created in setUp()
        $this->assertEquals(3, $user_tasks->count());
        $this->assertEquals(3, $other_user_tasks->count());

        // Verify all tasks belong to the correct user
        $user_tasks->each(function ($task) {
            $this->assertEquals($this->user->id, $task->user_id);
        });

        $other_user_tasks->each(function ($task) use ($other_user) {
            $this->assertEquals($other_user->id, $task->user_id);
        });
    }

    /** @test */
    public function task_scopes_can_be_chained()
    {
        // Create tasks with various combinations
        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => true,
            'priority' => 'low',
            'due_date' => now()->addDay(),
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'is_completed' => false,
            'priority' => 'low',
            'due_date' => now()->addDay(),
        ]);

        // Test chaining multiple scopes
        $tasks = Task::forUser($this->user->id)
            ->completed()
            ->priority('low')
            ->dueDate(now()->addDay()->format('Y-m-d'))
            ->get();

        $this->assertEquals(1, $tasks->count());
        $task = $tasks->first();

        $this->assertEquals($this->user->id, $task->user_id);
        $this->assertEquals(true, $task->is_completed);
        $this->assertEquals('low', $task->priority->value);
        $this->assertEquals(
            now()->addDay()->format('Y-m-d'),
            $task->due_date->format('Y-m-d')
        );
    }

    /** @test */
    public function task_handles_null_values_correctly()
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'description' => null,
            'due_date' => null,
        ]);

        $this->assertNull($task->description);
        $this->assertNull($task->due_date);

        // Test search array with null values
        $searchArray = $task->toSearchableArray();
        $this->assertNull($searchArray['description']);
        $this->assertNull($searchArray['due_date']);
    }

    /** @test */
    public function task_timestamps_are_updated_correctly()
    {
        $originalCreatedAt = $this->task->created_at;
        $originalUpdatedAt = $this->task->updated_at;

        sleep(1);
        $this->task->update(['title' => 'Updated Title']);

        $this->assertEquals(
            $originalCreatedAt->timestamp,
            $this->task->created_at->timestamp
        );
        $this->assertGreaterThan(
            $originalUpdatedAt->timestamp,
            $this->task->updated_at->timestamp
        );
    }

    /** @test */
    public function task_soft_deletes_work_correctly()
    {
        $taskId = $this->task->id;
        $this->task->delete();

        // Task shouldn't be found in normal queries c
        $this->assertNull(Task::find($taskId));

        // Task should be found when including trashed
        $this->assertNotNull(Task::withTrashed()->find($taskId));

        // Task should be restoreble
        $this->task->restore();
        $this->assertNotNull(Task::find($taskId));
    }
}
