<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 27.01.18
 * Time: 10:18
 */

namespace Core\Aggregates\Task\Events\LearningUnit;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Structs\Task;

class ASingleLearningUnitWasCreatedByAStudent
{
    public $task;

    public $student;

    public function __construct(Task $task, Student $student)
    {
        $this->task = $task;

        $this->student = $student;
    }
}