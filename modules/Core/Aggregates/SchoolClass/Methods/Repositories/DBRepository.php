<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 15:17
 */

namespace Core\Aggregates\SchoolClass\Methods\Repositories;


use Core\Abstracts\Aggregate\Exceptions\NonValidEntityException;
use Core\Abstracts\Aggregate\Methods\Repositories\AbstractDBRepository;
use Core\Aggregates\SchoolClass\Contracts\Repositories\DBRepositoryInterface;
use Core\Aggregates\SchoolClass\Events\SchoolClassWasCreated;
use Core\Aggregates\SchoolClass\Events\SchoolClassWasUpdated;
use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass as SchoolClassEntity;
use Illuminate\Support\Facades\DB;

class DBRepository extends AbstractDBRepository implements DBRepositoryInterface
{
    public function commit(): SchoolClass
    {
        $isNew = ! $this->rootEntity->exists;

        DB::transaction(function() {

            $prepared = $this->getAttributes();

            $this->rootEntity->setRawAttributes($prepared);

            $this->rootEntity->save();

        }, 5);

        $this->rootEntity = $this->rootEntity->fresh();

        $aggregate = new SchoolClass(
            $this->rootEntity->toArray()
        );

        if ($isNew === true) {
            event(new SchoolClassWasCreated($aggregate));
        }
        else {
            event(new SchoolClassWasUpdated($aggregate));
        }

        return $aggregate;
    }

    /**
     * @param $id
     * @return SchoolClass
     * @throws NonValidEntityException
     */
    public function findById($id): SchoolClass
    {
        if ($this->rootEntity->id === $id) {
            return new SchoolClass(
                $this->rootEntity->toArray()
            );
        }

        $this->rootEntity = $this->rootEntity->find($id);

        if (! $this->rootEntity instanceof SchoolClassEntity) {
            throw new NonValidEntityException('student entity not valid for id: ' . $id);
        }

        return new SchoolClass(
            $this->rootEntity->toArray()
        );
    }
}