<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 09:34
 */

namespace Core\Aggregates\Subject\Methods\Repositories;


use Core\Abstracts\Aggregate\Exceptions\NonValidEntityException;
use Core\Abstracts\Aggregate\Methods\Repositories\AbstractDBRepository;
use Core\Aggregates\Subject\Contracts\Repositories\DBRepositoryInterface;
use Core\Aggregates\Subject\Events\SubjectWasCreated;
use Core\Aggregates\Subject\Events\SubjectWasUpdated;
use Core\Aggregates\Subject\Structs\Subject;
use Core\Aggregates\Subject\Structs\Entities\Subject as SubjectEntity;
use Illuminate\Support\Facades\DB;

class DBRepository extends AbstractDBRepository implements DBRepositoryInterface
{
    /**
     * @return Subject
     */
    public function commit(): Subject
    {
        $isNew = ! $this->rootEntity->exists;

        DB::transaction(function() {

            $prepared = $this->getAttributes();

            $this->rootEntity->setRawAttributes($prepared);

            $this->rootEntity->save();

        }, 5);

        $this->rootEntity = $this->rootEntity->fresh();

        $aggregate = new Subject(
            $this->rootEntity->toArray()
        );

        if ($isNew === true) {
            event(new SubjectWasCreated($aggregate));
        }
        else {
            event(new SubjectWasUpdated($aggregate));
        }

        return $aggregate;
    }

    /**
     * @param $id
     * @return Subject
     * @throws NonValidEntityException
     */
    public function findById($id): Subject
    {
        if ($this->rootEntity->id === $id) {
            return new Subject(
                $this->rootEntity->toArray()
            );
        }

        $this->rootEntity = $this->rootEntity->find($id);

        if (! $this->rootEntity instanceof SubjectEntity) {
            throw new NonValidEntityException('student entity not valid for id: ' . $id);
        }

        return new Subject(
            $this->rootEntity->toArray()
        );
    }
}