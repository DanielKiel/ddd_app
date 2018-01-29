<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 29.01.18
 * Time: 15:46
 */

namespace Core\Aggregates\Task\Events\Exam;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Structs\Task;

class AnExamWasFinishedByStudent
{
    public $task;

    public $student;

    public function __construct(Task $task, Student $student)
    {
        $this->task = $task;

        $this->student = $student;
    }
}