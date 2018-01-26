<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 14:48
 */

namespace Core\Aggregates\Task\Structs;


use Core\Abstracts\Aggregate\Structs\AbstractRootEntity;
use Core\Aggregates\Task\Events\Homework\AHomeworkSerieWasCreated;
use Core\Aggregates\Task\Events\Homework\ASingleHomeworkWasCreated;

class Task extends AbstractRootEntity
{
    /**
     * @param $studentId
     * @param $subjectId
     * @param array $homework
     */
    public function createASingleHomeworkByStudent($studentId, $subjectId, array $homework)
    {
        $this->struct = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'homework' => [
                $homework
            ]
        ];

        $this->refresh('Core_Task_DBRepo');

        event(new ASingleHomeworkWasCreated($this));
    }

    /**
     * @param $studentId
     * @param $subjectId
     * @param array $homeworkSeries
     */
    public function createAHomeworkSeriesByStudent($studentId, $subjectId, array $homeworkSeries)
    {
        $this->struct = [
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'status' => 'open',
            'homework' => $homeworkSeries
        ];

        $this->refresh('Core_Task_DBRepo');

        event(new AHomeworkSerieWasCreated($this));
    }
}