<?php

namespace Core\Aggregates\Subject\Structs\Entities;

use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Core\Aggregates\Task\Structs\Entities\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $fillable = ['name'];

    /**
     * @return BelongsToMany
     */
    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'school_class_subject');
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'subject_id');
    }
}
