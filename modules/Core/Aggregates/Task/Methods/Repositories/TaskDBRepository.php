<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 14:42
 */

namespace Core\Aggregates\Task\Methods\Repositories;


use Core\Abstracts\Aggregate\Exceptions\NonValidEntityException;
use Core\Abstracts\Aggregate\Methods\Repositories\AbstractDBRepository;
use Core\Aggregates\Task\Contracts\Repositories\TaskDBRepositoryInterface;
use Core\Aggregates\Task\Events\TaskWasCreated;
use Core\Aggregates\Task\Events\TaskWasUpdated;
use Core\Aggregates\Task\Structs\Entities\Exam;
use Core\Aggregates\Task\Structs\Entities\Homework;
use Core\Aggregates\Task\Structs\Entities\LearningUnit;
use Core\Aggregates\Task\Structs\Task;
use Core\Aggregates\Task\Structs\Entities\Task as TaskEntity;
use Illuminate\Support\Facades\DB;

class TaskDBRepository extends AbstractDBRepository implements TaskDBRepositoryInterface
{
    /**
     * @return Task
     */
    public function commit(): Task
    {
        $isNew = ! $this->rootEntity->exists;

        DB::transaction(function() {

            $prepared = $this->getAttributes();

            $this->rootEntity->setRawAttributes($prepared);

            $this->rootEntity->save();

            $this->addHomework()
                ->addLearningUnits();

        }, 5);

        $aggregate = $this->aggregate();

        if ($isNew === true) {
            event(new TaskWasCreated($aggregate));
        }
        else {
            event(new TaskWasUpdated($aggregate));
        }

        return $aggregate;
    }

    /**
     * @param $id
     * @return Task
     * @throws NonValidEntityException
     */
    public function findById($id): Task
    {
        if ($this->rootEntity->id === $id) {
            return $this->aggregate();
        }

        $this->rootEntity = $this->rootEntity->find($id);

        if (! $this->rootEntity instanceof TaskEntity) {
            throw new NonValidEntityException('student entity not valid for id: ' . $id);
        }

        return $this->aggregate();
    }

    /**
     * @return Task
     */
    public function aggregate():Task
    {
        $this->rootEntity = $this->rootEntity->fresh(['homework', 'learningUnits', 'exams']);

        return new Task(
            $this->rootEntity->toArray()
        );
    }

    /**
     * @return $this
     */
    protected function addHomework()
    {
        $homework = array_get($this->struct, 'homework', []);

        if (!empty($homework)) {
            foreach ($homework as $key => $work) {
                if (array_has($work, 'id')) {
                    continue;
                }

                array_set($work, 'task_id', $this->rootEntity->id);

                Homework::create($work);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function addLearningUnits()
    {
        $learningUnits = array_get($this->struct, 'learning_units', []);

        if (!empty($learningUnits)) {
            foreach ($learningUnits as $key => $work) {
                if (array_has($work, 'id')) {
                    continue;
                }

                array_set($work, 'task_id', $this->rootEntity->id);

                LearningUnit::create($work);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function addExams()
    {
        $exams = array_get($this->struct, 'exams', []);

        if (!empty($exams)) {
            foreach ($exams as $key => $exam) {
                if (array_has($exam, 'id')) { 
                    continue;
                }

                array_set($exam, 'task_id', $this->rootEntity->id);

                Exam::create($exam);
            }
        }

        return $this;
    }
}