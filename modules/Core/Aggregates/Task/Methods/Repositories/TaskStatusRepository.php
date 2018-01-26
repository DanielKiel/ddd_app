<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 20:13
 */

namespace Core\Aggregates\Task\Methods\Repositories;


use Core\Aggregates\Task\Events\Homework\AHomeworkWasFinishedByAStudent;
use Core\Aggregates\Task\Events\TaskWasClosedByAStudent;
use Core\Aggregates\Task\Structs\Task;

class TaskStatusRepository
{
    /**
     * @param AHomeworkWasFinishedByAStudent $event
     */
    public function onHomeworkWasFinishedByAStudent(AHomeworkWasFinishedByAStudent $event)
    {
        if ($this->isACloseableTask($event->task) === true) {
            $event->task->close();
            $event->task->refresh('Core_Task_DBRepo');

            event(new TaskWasClosedByAStudent($event->task, $event->student));
        }

    }

    /**
     * Register the listeners for the subscriber.
     *
     */
    public function subscribe($events)
    {
        $events->listen(
           AHomeworkWasFinishedByAStudent::class,
           get_class($this) . '@onHomeworkWasFinishedByAStudent'
        );
    }

    /**
     * @param Task $task
     * @return bool
     */
    protected function isACloseableTask(Task $task): bool
    {
        $isCloseable = true;

        $homework = $task->homework;

        if (! is_array($homework)) {
            $homework = [];
        }

        foreach ($homework as $el) {
            if (array_get($el, 'status') !== 'closed') {
                $isCloseable = false;

                break;
            }
        }
        
        return $isCloseable;
    }

}