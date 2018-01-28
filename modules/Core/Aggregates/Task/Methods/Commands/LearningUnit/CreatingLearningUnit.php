<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 27.01.18
 * Time: 10:27
 */

namespace Core\Aggregates\Task\Methods\Commands\LearningUnit;


use Core\Aggregates\Task\Events\LearningUnit\ALearningUnitSerieWasCreatedByAStudent;
use Core\Aggregates\Task\Events\LearningUnit\ASingleLearningUnitWasCreatedByAStudent;
use Core\Aggregates\Task\Structs\Task;

class CreatingLearningUnit
{
    /**
     * @param $studentId
     * @param $subjectId
     * @param array $learningUnit
     * @return Task
     */
    public function createASingleLearningUnitByStudent($studentId, $subjectId, array $learningUnit): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'learning_units' => [
                $learningUnit
            ]
        ])->commit();

        event(new ASingleLearningUnitWasCreatedByAStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }

    /**
     * @param $studentId
     * @param $subjectId
     * @param array $learningUnitSeries
     * @return Task
     */
    public function createALearningUnitSeriesByStudent($studentId, $subjectId, array $learningUnitSeries): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'learning_units' => $learningUnitSeries
        ])->commit();

        event(new ALearningUnitSerieWasCreatedByAStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }
}