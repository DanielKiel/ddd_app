<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Homework extends Model
{
    protected $table = 'homeworks';

    protected $fillable = [
        'deadline', 'todo', 'content', 'status', 'task_id'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

}
