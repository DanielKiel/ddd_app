<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 15:15
 */

namespace Core\Aggregates\Subject\Events;


use Core\Aggregates\Subject\Structs\Subject;

class SubjectWasCreated
{
    public $subject;

    public function __construct(Subject $subject)
    {
        $this->subject = $subject;
    }
}