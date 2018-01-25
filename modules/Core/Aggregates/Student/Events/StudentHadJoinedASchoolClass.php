<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 19:28
 */

namespace Core\Aggregates\Student\Events;


use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\Student\Structs\Student;

class StudentHadJoinedASchoolClass
{
    public $student;

    public $schoolClass;

    public function __construct(Student $student, SchoolClass $schoolClass)
    {
        $this->student = $student;

        $this->schoolClass = $schoolClass;
    }
}