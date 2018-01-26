<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 14:53
 */

namespace Core\Aggregates\Task\Events;


use Core\Aggregates\Task\Structs\Task;

class TaskWasUpdated
{
    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}