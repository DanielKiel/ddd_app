<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 15:14
 */

namespace Core\Aggregates\SchoolClass\Structs;


use Core\Abstracts\Aggregate\Structs\AbstractRootEntity;
use Core\Aggregates\SchoolClass\Events\SchoolClassGotANewSubject;
use Core\Aggregates\Subject\Structs\Subject;

class SchoolClass extends AbstractRootEntity
{
    public function assignANewSubject(Subject $subject)
    {
        $subjectArray = $subject->toArray();
        $subjects = $this->struct->get('subjects', []);

        //avoid joining a class twice!
        $exists = array_first($subjects, function($subject) use ($subjectArray) {
            return array_get($subject, 'id') === array_get($subjectArray, 'id');
        });

        if (! empty($exists)) {
            return;
        }

        array_push($subjects, $subjectArray);

        $this->struct->put('subjects', $subjects);

        app()->make('Core_SchoolClass_DBRepo')
            ->setStruct($this->struct->toArray())
            ->commit();

        event(new SchoolClassGotANewSubject(
            $this,
            $subject
        ));
    }
}