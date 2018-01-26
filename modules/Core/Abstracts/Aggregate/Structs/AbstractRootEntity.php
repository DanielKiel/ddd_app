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

    /**
     * AbstractRootEntity constructor.
     * @param array $struct
     */
    public function __construct(array $struct)
    {
        $this->struct = collect($struct);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->struct->toArray();
    }

    /**
     * @param $repoName
     */
    public function refresh($repoName)
    {
        $repo = app()->make($repoName);

        $repo->setStruct($this->struct->toArray())
            ->commit();

        $this->struct = collect($repo->findById($this->id)
            ->toArray());
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->struct->get($name);
    }
}