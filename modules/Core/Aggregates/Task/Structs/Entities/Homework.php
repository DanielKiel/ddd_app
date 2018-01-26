<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Core\Aggregates\Task\Structs\ValueObjects\Deadline;
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

    public function toArray()
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'deadline' => (new Deadline($this->deadline))->show(),
            'todo' => $this->todo,
            'content' => $this->content
        ];
    }

}
