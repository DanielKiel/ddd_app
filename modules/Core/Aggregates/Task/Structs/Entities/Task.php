<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Core\Aggregates\Student\Structs\Entities\Student;
use Core\Aggregates\Subject\Structs\Entities\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'student_id', 'subject_id', 'status'
    ];

    /**
     * @return BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * @return BelongsTo
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class, 'task_id');
    }
}
