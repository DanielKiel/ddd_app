<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 09:49
 */

namespace Core\Aggregates\SchoolClass\Events;


use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\Subject\Structs\Subject;

class SchoolClassGotANewSubject
{
    public $schoolClass;

    public $subject;

    public function __construct(SchoolClass $schoolClass, Subject $subject)
    {
        $this->schoolClass = $schoolClass;

        $this->subject = $subject;
    }
}