<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 20:13
 */

namespace Core\Aggregates\Task\Methods\Repositories;


use Core\Aggregates\Task\Events\Homework\AHomeworkWasFinishedByAStudent;
use Core\Aggregates\Task\Events\LearningUnit\ALearningUnitWasFinishedByAStudent;
use Core\Aggregates\Task\Events\TaskWasClosedByAStudent;
use Core\Aggregates\Task\Structs\Task;

class TaskStatusRepository
{
    /**
     * @param $event
     */
    public function checkIfTaskIsCloseable($event)
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
           get_class($this) . '@checkIfTaskIsCloseable'
        );

        $events->listen(
            ALearningUnitWasFinishedByAStudent::class,
            get_class($this) . '@checkIfTaskIsCloseable'
        );
    }

    /**
     * @param Task $task
     * @return bool
     */
    protected function isACloseableTask(Task $task): bool
    {
        $isCloseable = true;

        $properties = ['homework', 'learning_units'];

        foreach($properties as $property) {
            $subjects = $task->{$property};

            if (! is_array($subjects)) {
                $subjects = [];
            }

            foreach ($subjects as $subject) {
                if (array_get($subject, 'status') !== 'closed') {
                    $isCloseable = false;

                    break;
                }
            }

            if ($isCloseable === false) {
                return $isCloseable;
            }
        }

        return $isCloseable;
    }

}