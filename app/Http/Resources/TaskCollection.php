<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\TaskResource;

class TaskCollection extends ResourceCollection
{
    public $collects = TaskResource::class;
}
