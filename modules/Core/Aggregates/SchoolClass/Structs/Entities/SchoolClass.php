<?php

namespace Core\Aggregates\SchoolClass\Structs\Entities;

use Core\Aggregates\Student\Structs\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    protected $table = 'school_classes';

    protected $fillable = ['name'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'school_class_student');
    }
}
