<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = [
        'task_id', 'scheduled', 'status', 'grading', 'content'
    ];

    protected $dates = [
        'created_at', 'updated_at', 'scheduled'
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }
}
