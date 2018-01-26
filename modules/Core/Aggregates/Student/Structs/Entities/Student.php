<?php

namespace Core\Aggregates\Student\Structs\Entities;

use App\User;
use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Core\Aggregates\Subject\Structs\Entities\Subject;
use Core\Aggregates\Task\Structs\Entities\Task;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Student extends Model
{
    protected $table = 'students';

    protected $fillable = [];

    public function account(): MorphOne
    {
        return $this->morphOne(User::class, 'accountable');
    }

    /**
     * @return BelongsToMany
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'school_class_student');
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'student_id');
    }

    /**
     * @return Collection
     */
    public function subjects(): Collection
    {
        //@TODO this can be done better I think.
        $result = DB::table('subjects')
            ->join('school_class_subject', 'subjects.id', '=', 'school_class_subject.subject_id')
            ->whereIn(
                'school_class_subject.school_class_id',
                $this->classes()->pluck('school_classes.id')->toArray())
            ->get();
        return $result;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = parent::toArray();

        $subjects = [];

        $this->subjects()->each(function($subject) use(&$subjects) {
            array_push($subjects, new \Core\Aggregates\Subject\Structs\Subject((array) $subject));
        });

        array_set($array, 'subjects', $subjects);

        return $array;
    }

}
