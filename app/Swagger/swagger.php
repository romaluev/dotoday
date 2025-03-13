<?php

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Task Management API",
 *     description="API documentation for Task Management System",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *     schema="ValidationError",
 *     @OA\Property(property="message", type="string", example="The given data was invalid."),
 *     @OA\Property(
 *         property="errors",
 *         type="object",
 *         @OA\AdditionalProperties(
 *             type="array",
 *             @OA\Items(type="string")
 *         )
 *     )
 * )
 * 
 * @OA\Schema(
 *     schema="TaskResource",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Complete project"),
 *     @OA\Property(property="description", type="string", nullable=true, example="Finish the task management system"),
 *     @OA\Property(property="status", type="string", enum={"todo", "in_progress", "completed"}, example="todo"),
 *     @OA\Property(property="priority", type="integer", minimum=0, maximum=5, example=1),
 *     @OA\Property(
 *         property="due_date",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="date", type="string", format="date", example="2025-03-10"),
 *         @OA\Property(property="time", type="string", format="time", example="14:30:00"),
 *         @OA\Property(property="formatted", type="string", format="date-time", example="2025-03-10 14:30:00"),
 *         @OA\Property(property="timestamp", type="integer", example=1709147400)
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="object",
 *         @OA\Property(property="id", type="integer", example=1),
 *         @OA\Property(property="name", type="string", example="John Doe"),
 *         @OA\Property(property="username", type="string", example="johndoe")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-03-09 01:30:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-03-09 01:30:00")
 * )
 * 
 * @OA\Schema(
 *     schema="TaskCollection",
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/TaskResource")
 *     ),
 *     @OA\Property(
 *         property="meta",
 *         type="object",
 *         @OA\Property(property="total", type="integer", example=20),
 *         @OA\Property(property="per_page", type="integer", example=15),
 *         @OA\Property(property="current_page", type="integer", example=1),
 *         @OA\Property(property="last_page", type="integer", example=2),
 *         @OA\Property(property="from", type="integer", example=1),
 *         @OA\Property(property="to", type="integer", example=15)
 *     )
 * )
 */