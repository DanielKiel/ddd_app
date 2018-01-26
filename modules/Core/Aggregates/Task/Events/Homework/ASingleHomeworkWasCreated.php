<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 15:15
 */

namespace Core\Aggregates\Task\Events\Homework;


use Core\Aggregates\Task\Structs\Task;

class ASingleHomeworkWasCreated
{
    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}