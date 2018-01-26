<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 10:38
 */

namespace Core\Aggregates\Student\Methods\Repositories;


use App\User;
use Core\Abstracts\Aggregate\Exceptions\NonValidEntityException;
use Core\Abstracts\Aggregate\Methods\Repositories\AbstractDBRepository;
use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Core\Aggregates\Student\Contracts\Repositories\DBRepositoryInterface;
use Core\Aggregates\Student\Events\StudentWasCreated;
use Core\Aggregates\Student\Events\StudentWasUpdated;
use Core\Aggregates\Student\Structs\Student;
use Illuminate\Support\Facades\DB;
use Core\Aggregates\Student\Structs\Entities\Student as StudentEntity;

class DBRepository extends AbstractDBRepository implements DBRepositoryInterface
{
    /**
     * @return Student
     */
    public function commit(): Student
    {
        $isNew = ! $this->rootEntity->exists;

        DB::transaction(function() use($isNew) {

            $prepared = $this->getAttributes();

            $this->rootEntity->setRawAttributes($prepared);

            $this->rootEntity->save();

            $this->rootEntity = $this->rootEntity->fresh();

            $this->addAccount()
                ->addClasses();

        }, 5);

        $this->rootEntity = $this->rootEntity->fresh(['account']);

        $aggregate = new Student(
            $this->rootEntity->toArray()
        );

        if ($isNew === true) {
            event(new StudentWasCreated($aggregate));
        }
        else {
            event(new StudentWasUpdated($aggregate));
        }

        return $aggregate;
    }

    /**
     * @param $id
     * @return Student
     * @throws NonValidEntityException
     */
    public function findById($id): Student
    {
        if ($this->rootEntity->id === $id) {
            return new Student(
                $this->rootEntity->toArray()
            );
        }

        $this->rootEntity = $this->rootEntity->find($id);

        if (! $this->rootEntity instanceof StudentEntity) {
            throw new NonValidEntityException('student entity not valid for id: ' . $id);
        }

        return new Student(
            $this->rootEntity->toArray()
        );
    }

    /**
     * @return $this
     */
    protected function addAccount()
    {
        $account = array_get($this->struct, 'account', []);

        if (! empty($account)) {

            if ($this->rootEntity->account instanceof User) {
                $this->rootEntity->account->update($account);
            }
            else {
                $this->rootEntity->account()->save(new User($account));
            }
        }

        return $this;
    }

    /**
     * its not at this repository - but we must be aware that we only add classes once
     * @return $this
     */
    protected function addClasses()
    {
        $classes = array_get($this->struct, 'classes', []);

        if (!empty($classes)) {
            $insert = [];
            foreach ($classes as $key => $class) {
                $insert[] = [
                    'school_class_id' => array_get($class, 'id'),
                    'student_id' => $this->rootEntity->id
                ];
            }

            DB::table('school_class_student')
                ->insert($insert);
        }

        return $this;
    }
}