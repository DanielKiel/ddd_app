<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningUnit extends Model
{
    protected $table = 'learning_units';

    protected $fillable = [
        'task_id', 'status', 'scheduled', 'estimated_time', 'todo', 'content'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'scheduled'
    ];

    protected $casts = [
        'estimated_time' => 'integer'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
