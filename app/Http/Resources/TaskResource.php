<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->when($this->description, $this->description),
            'is_completed' => $this->is_completed,
            'due_date' => $this->when($this->due_date, function() {
                return [
                    'date' => $this->due_date?->format('Y-m-d'),
                    'time' => $this->due_date?->format('H:i:s'),
                    'formatted' => $this->due_date?->format('Y-m-d H:i:s'),
                    'timestamp' => $this->due_date?->timestamp,
                ];
            }),
            'priority' => $this->priority->value,
            'author' => new UserResource($this->whenLoaded('author')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
