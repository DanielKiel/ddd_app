<?php

namespace Core\Aggregates\Subject\Structs\Entities;

use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
