<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 15:57
 */

namespace Core\Aggregates\Task\Structs\ValueObjects;


use Core\Abstracts\Aggregate\Structs\ValueObjects\DateTimeObject;

class Deadline extends DateTimeObject
{
    public function show()
    {
        return (string) $this->value;
    }
}