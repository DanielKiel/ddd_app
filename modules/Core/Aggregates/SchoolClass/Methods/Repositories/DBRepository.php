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

            $this->addSubjects();

        }, 5);

        $aggregate = $this->aggregate();

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
            return $this->aggregate();
        }

        $this->rootEntity = $this->rootEntity->find($id);

        if (! $this->rootEntity instanceof SchoolClassEntity) {
            throw new NonValidEntityException('student entity not valid for id: ' . $id);
        }

        return $this->aggregate();
    }

    /**
     * @return SchoolClass
     */
    public function aggregate()
    {
        $this->rootEntity = $this->rootEntity->fresh(['subjects']);

        return new SchoolClass(
            $this->rootEntity->toArray()
        );
    }

    /**
     * its not at this repository - but we must be aware that we only add subjects once
     * @return $this
     */
    protected function addSubjects()
    {
        $subjects = array_get($this->struct, 'subjects', []);
 
        if (!empty($subjects)) {
            $insert = [];
            foreach ($subjects as $key => $subject) {
                if (array_has($subject, 'pivot')) {
                    continue;
                }

                $exists = DB::table('school_class_subject')
                    ->where('subject_id', array_get($subject, 'id'))
                    ->where('school_class_id', $this->rootEntity->id)
                    ->exists();

                if ((bool) $exists === true) {
                    continue;
                }

                $insert[] = [
                    'subject_id' => array_get($subject, 'id'),
                    'school_class_id' => $this->rootEntity->id
                ];
            }

            if (!empty($insert)) {
                DB::table('school_class_subject')
                    ->insert($insert);
            }

        }

        return $this;
    }
}