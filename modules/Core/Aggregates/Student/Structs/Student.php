<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 11:03
 */

namespace Core\Aggregates\Student\Structs;


use Core\Abstracts\Aggregate\Structs\AbstractRootEntity;
use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\Student\Events\StudentHadJoinedASchoolClass;
use Core\Aggregates\Student\Events\StudentHadLeavedASchoolClass;
use Illuminate\Support\Facades\DB;

class Student extends AbstractRootEntity
{
    /**
     * @param SchoolClass $class
     */
    public function joinsASchoolClass(SchoolClass $class)
    {
        $schoolClass = $class->toArray();
        $classes = $this->struct->get('classes', []);

        //avoid joining a class twice!
        $exists = array_first($classes, function($class) use ($schoolClass) {
            return array_get($class, 'id') === array_get($schoolClass, 'id');
        });

        if (! empty($exists)) {
            return;
        }

        array_push($classes, $schoolClass);

        $this->struct->put('classes', $classes);

        app()->make('Core_Student_DBRepo')
            ->setStruct($this->struct->toArray())
            ->commit();

        event(new StudentHadJoinedASchoolClass(
           $this,
           $class
        ));
    }

    /**
     * @param SchoolClass $schoolClass
     */
    public function leavesASchoolClass(SchoolClass $schoolClass)
    {
        $classes = $this->struct->get('classes', []);

        //avoid joining a class twice!
        $exists = array_first($classes, function($class) use ($schoolClass) {
            return array_get($class, 'id') === $schoolClass->id;
        });

        if (empty($exists)) {
            return;
        }

        //@TODO think about how to solve this really at repo correct!!!
        $studentID = $this->id;
        $schoolClassID = $schoolClass->id;

        $result = DB::table('school_class_student')
            ->where('school_class_id', $schoolClassID)
            ->where('student_id', $studentID)
            ->delete();

        if ((bool) $result === false) {

            return;
        }

        $classes = array_filter($classes, function($class) use($schoolClass) {
            return array_get($class, 'id') !== $schoolClass->id;
        });

        $this->struct->put('classes', $classes);

        event(new StudentHadLeavedASchoolClass(
            $this,
            $schoolClass
        ));
    }
}