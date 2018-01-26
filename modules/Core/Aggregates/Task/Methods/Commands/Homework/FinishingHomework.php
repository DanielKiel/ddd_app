<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 19:59
 */

namespace Core\Aggregates\Task\Methods\Commands\Homework;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Events\Homework\AHomeworkWasFinishedByAStudent;
use Core\Aggregates\Task\Structs\Entities\Homework;
use Core\Aggregates\Task\Structs\Task;

class FinishingHomework
{
    /**
     * @param $homeworkId
     * @param Student $student
     * @return Task
     */
    public function finishingHomeworkByAStudent($homeworkId, Student $student): Task
    {
        $homework = Homework::find($homeworkId);
        $homework->update([
            'status' => 'closed'
        ]);

        $repo = app()->make('Core_Task_DBRepo');
        $task = $repo->findById($homework->task_id);

        event(new AHomeworkWasFinishedByAStudent($task, $student));

        return $task;
    }
}