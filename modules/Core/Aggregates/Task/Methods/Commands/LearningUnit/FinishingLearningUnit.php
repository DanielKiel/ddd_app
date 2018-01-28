<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 28.01.18
 * Time: 10:42
 */

namespace Core\Aggregates\Task\Methods\Commands\LearningUnit;


use Core\Aggregates\Student\Structs\Student;
use Core\Aggregates\Task\Events\LearningUnit\ALearningUnitWasFinishedByAStudent;
use Core\Aggregates\Task\Structs\Entities\LearningUnit;

class FinishingLearningUnit
{
    public function finishingLearningUnitByAStudent($learningUnitId, Student $student)
    {
        $learningUnit = LearningUnit::find($learningUnitId);
        $learningUnit->update([
            'status' => 'closed'
        ]);

        $repo = app()->make('Core_Task_DBRepo');
        $task = $repo->findById($learningUnit->task_id);

        event(new ALearningUnitWasFinishedByAStudent($task, $student));

        return $task;
    }
}