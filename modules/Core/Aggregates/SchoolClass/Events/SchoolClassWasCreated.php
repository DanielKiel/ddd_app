<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 15:15
 */

namespace Core\Aggregates\SchoolClass\Events;


use Core\Aggregates\SchoolClass\Structs\SchoolClass;

class SchoolClassWasCreated
{
    public $schoolClass;

    public function __construct(SchoolClass $schoolClass)
    {
        $this->schoolClass = $schoolClass;
    }
}