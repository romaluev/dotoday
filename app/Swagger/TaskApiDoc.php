<?php

namespace App\Swagger;

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="API Endpoints for task management"
 * )
 */
class TaskApiDoc
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="List all tasks",
     *     description="Get a paginated list of tasks for the authenticated user with optional filtering and sorting",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by task status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"todo", "in_progress", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority level (0-5)",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0, maximum=5)
     *     ),
     *     @OA\Parameter(
     *         name="due_date",
     *         in="query",
     *         description="Filter by due date (Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"created_at", "updated_at", "due_date", "priority", "status"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TaskCollection")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     )
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     description="Create a new task for the authenticated user",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "status", "priority"},
     *             @OA\Property(property="title", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"todo", "in_progress", "completed"}),
     *             @OA\Property(property="due_date", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="priority", type="integer", minimum=0, maximum=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaskResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Get a specific task",
     *     description="Retrieve details of a specific task",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TaskResource")
     *     ),
     *     @OA\Response(response=404, description="Task not found"),
     *     @OA\Response(response=403, description="Unauthorized action"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Update a task",
     *     description="Update details of a specific task",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="status", type="string", enum={"todo", "in_progress", "completed"}),
     *             @OA\Property(property="due_date", type="string", format="date-time", nullable=true),
     *             @OA\Property(property="priority", type="integer", minimum=0, maximum=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TaskResource")
     *     ),
     *     @OA\Response(response=404, description="Task not found"),
     *     @OA\Response(response=403, description="Unauthorized action"),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     tags={"Tasks"},
     *     summary="Delete a task",
     *     description="Remove a specific task",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Task deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Task not found"),
     *     @OA\Response(response=403, description="Unauthorized action"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy() {}

    /**
     * @OA\Get(
     *     path="/api/tasks/search",
     *     tags={"Tasks"},
     *     summary="Search tasks",
     *     description="Search tasks by query string with optional filters",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query string",
     *         required=true,
     *         @OA\Schema(type="string", minLength=2)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"todo", "in_progress", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter by priority",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=0, maximum=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/TaskCollection")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(ref="#/components/schemas/ValidationError")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function search() {}
}