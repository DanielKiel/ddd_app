<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 14:48
 */

namespace Core\Aggregates\Task\Structs;


use Core\Abstracts\Aggregate\Structs\AbstractRootEntity;

class Task extends AbstractRootEntity
{
    public function close()
    {
        $this->struct->put('status', 'closed');
    }
}