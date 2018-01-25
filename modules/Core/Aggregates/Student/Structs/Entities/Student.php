<?php

namespace Core\Aggregates\Student\Structs\Entities;

use App\User;
use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [];

    public function account(): MorphOne
    {
        return $this->morphOne(User::class, 'accountable');
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'school_class_student');
    }


}
