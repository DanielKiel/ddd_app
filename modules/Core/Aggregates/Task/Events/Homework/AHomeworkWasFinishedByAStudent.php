<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 20:09
 */

namespace Core\Aggregates\Task\Events\Homework;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Structs\Task;

class AHomeworkWasFinishedByAStudent
{
    public $task;

    public $student;

    public function __construct(Task $task, Student $student)
    {
        $this->task = $task;

        $this->student = $student;
    }
}