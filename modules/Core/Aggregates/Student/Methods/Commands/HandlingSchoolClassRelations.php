<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 08:15
 */

namespace Core\Aggregates\Student\Methods\Commands;


use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\Student\Events\StudentHadJoinedASchoolClass;
use Core\Aggregates\Student\Events\StudentHadLeavedASchoolClass;
use Illuminate\Support\Facades\DB;

trait HandlingSchoolClassRelations
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

        // it will be necessary to refresh the struct -cause when joining a school class it means joining the assigned subjects of
        // a school class and we want to see them at the struct
        $repo = app()->make('Core_Student_DBRepo');

        $repo->setStruct($this->struct->toArray())
            ->commit();

        $this->struct = collect($repo->findById($this->id)
            ->toArray());

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