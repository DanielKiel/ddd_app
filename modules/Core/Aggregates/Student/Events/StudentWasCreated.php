<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 13:47
 */

namespace Core\Aggregates\Student\Events;


use Core\Aggregates\Student\Structs\Student;

class StudentWasCreated
{
    /** @var Student  */
    public $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }
}