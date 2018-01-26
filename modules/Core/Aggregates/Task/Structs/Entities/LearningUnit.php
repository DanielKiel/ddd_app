<?php

namespace Core\Aggregates\Task\Structs\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class LearningUnit extends Model
{
    protected $table = 'learning_units';

    protected $fillable = [];

    public function account(): MorphOne
    {
        return $this->morphOne(Task::class, 'taskable');
    }
}
