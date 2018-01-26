<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Exam extends Model
{
    protected $table = 'exams';

    protected $fillable = [];

    public function account(): MorphOne
    {
        return $this->morphOne(Task::class, 'taskable');
    }
}
