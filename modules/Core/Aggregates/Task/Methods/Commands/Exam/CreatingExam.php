<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 29.01.18
 * Time: 15:47
 */

namespace Core\Aggregates\Task\Methods\Exam;


use Core\Aggregates\Task\Events\Exam\AnExamSeriesWasCreatedByStudent;
use Core\Aggregates\Task\Events\Exam\AnExamWasCreatedByStudent;
use Core\Aggregates\Task\Structs\Task;

class CreatingExam
{
    /**
     * @param $studentId
     * @param $subjectId
     * @param array $exam
     * @return Task
     */
    public function createAnExamByStudent($studentId, $subjectId, array $exam): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'exams' => [
                $exam
            ]
        ])->commit();

        event(new AnExamWasCreatedByStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }

    /**
     * @param $studentId
     * @param $subjectId
     * @param array $examSeries
     * @return Task
     */
    public function createAnExamSeriesByStudent($studentId, $subjectId, array $examSeries): Task
    {
        $repo = app()->make('Core_Task_DBRepo');

        $result = $repo->setStruct([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'exams' => $examSeries
        ])->commit();

        event(new AnExamSeriesWasCreatedByStudent(
            $result,
            app()->make('Core_Student_DBRepo')->findById($studentId)
        ));

        return $result;
    }
}