<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 10:36
 */

namespace Core\Abstracts\Aggregate\Structs;

/**
 * Class AbstractRootEntity
 * @package Core\Abstracts\Aggregate\Structs
 */
abstract class AbstractRootEntity
{
    /** @var \Illuminate\Support\Collection  */
    protected $struct;

    public function __construct(array $struct)
    {
        $this->struct = collect($struct);
    }

    public function toArray()
    {
        return $this->struct->toArray();
    }

    public function __get($name)
    {
        return $this->struct->get($name);
    }
}