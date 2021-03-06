<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 15:26
 */

namespace Core\Aggregates\Task\Methods\Commands\Homework;


use Core\Aggregates\Task\Events\Homework\AHomeworkSerieWasCreatedByStudent;
use Core\Aggregates\Task\Events\Homework\ASingleHomeworkWasCreatedByStudent;
use Core\Aggregates\Task\Structs\Task;

class CreatingHomework
{
    /**
     * @param $studentId
     * @param $subjectId
     * @param array $homework
     * @return Task
     */
    public function createASingleHomeworkByStudent($studentId, $subjectId, array $homework): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'homework' => [
                $homework
            ]
        ])->commit();

        event(new ASingleHomeworkWasCreatedByStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }

    /**
     * @param $studentId
     * @param $subjectId
     * @param array $homeworkSeries
     * @return Task
     */
    public function createAHomeworkSeriesByStudent($studentId, $subjectId, array $homeworkSeries): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'homework' => $homeworkSeries
        ])->commit();

        event(new AHomeworkSerieWasCreatedByStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }
}