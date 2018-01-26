<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 11:03
 */

namespace Core\Aggregates\Student\Structs;


use Core\Abstracts\Aggregate\Structs\AbstractRootEntity;
use Core\Aggregates\Student\Methods\Commands\HandlingSchoolClassRelations;

/**
 * Class Student
 * @package Core\Aggregates\Student\Structs
 */
class Student extends AbstractRootEntity
{
    use HandlingSchoolClassRelations;
}