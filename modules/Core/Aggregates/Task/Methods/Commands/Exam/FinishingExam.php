<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 29.01.18
 * Time: 15:47
 */

namespace Core\Aggregates\Task\Methods\Commands\Exam;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Events\Exam\AnExamWasFinishedByStudent;
use Core\Aggregates\Task\Structs\Entities\Exam;

class FinishingExam
{
    /**
     * @param $examId
     * @param Student $student
     * @return mixed
     */
    public function finishingExamByAStudent($examId, Student $student)
    {
        $exam = Exam::find($examId);
        $exam->update([
            'status' => 'closed'
        ]);

        $repo = app()->make('Core_Task_DBRepo');
        $task = $repo->findById($exam->task_id);

        event(new AnExamWasFinishedByStudent($task, $student));

        return $task;
    }
}