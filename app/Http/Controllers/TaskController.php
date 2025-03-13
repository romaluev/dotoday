<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskFilterRequest;
use App\Http\Requests\TaskSearchRequest;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Display a listing of the tasks.
     *
     * @param TaskFilterRequest $request
     * @return JsonResponse
     */
    public function index(TaskFilterRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->where('user_id', Auth::id())
            ->when(isset($request->is_completed), fn($q) => $q->completed($request->is_completed))
            ->when($request->priority, fn($q) => $q->priority($request->priority))
            ->when($request->due_date, fn($q) => $q->dueDate($request->due_date))
            ->when(
                $request->sort_by,
                fn($q) => $q->orderBy($request->sort_by, $request->sort_order ?? 'desc'),
                fn($q) => $q->latest()
            )
            ->with('author')
            ->paginate($request->per_page ?? 15);

        return response()->json(
            new TaskCollection($tasks),
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created task.
     *
     * @param TaskStoreRequest $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);

        $task = Auth::user()->tasks()->create($request->validated());

        return response()->json(
            new TaskResource($task->load('author')),
            Response::HTTP_CREATED
        );
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json(
            new TaskResource($task->load('author')),
            Response::HTTP_OK
        );
    }

    /**
     * Update the specified task.
     *
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        $this->authorize('update', $task);

        $task->fill($request->validated());
        $task->save();

        return response()->json(
            new TaskResource($task->fresh()->load('author')),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified task.
     *
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorize('delete', $task);

        $task->delete();

        return response()->json(
            ['message' => __('messages.task.deleted')],
            Response::HTTP_OK
        );
    }

    /**
     * Search for tasks.
     *
     * @param TaskSearchRequest $request
     * @return JsonResponse
     */
    public function search(TaskSearchRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::search($request->query)
            ->where('user_id', Auth::id())
            ->when(isset($request->is_completed), fn($q) => $q->where('is_completed', $request->is_completed))
            ->when($request->priority, fn($q) => $q->where('priority', $request->priority))
            ->with('author')
            ->paginate($request->per_page ?? 15);

        return response()->json(
            new TaskCollection($tasks),
            Response::HTTP_OK
        );
    }
}
