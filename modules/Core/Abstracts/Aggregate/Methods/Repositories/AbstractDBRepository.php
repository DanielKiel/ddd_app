<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 09:51
 */

namespace Core\Abstracts\Aggregate\Methods\Repositories;


use Core\Abstracts\Aggregate\Exceptions\NonValidEntityException;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DBRepository
 * @package Core\Abstracts\Aggregate\Methods\Repositories
 */
abstract class AbstractDBRepository
{
    /** @var Model */
    protected $rootEntity;

    protected $struct;

    /**
     * DBRepository constructor.
     * @param string $class
     * @throws NonValidEntityException
     */
    public function __construct(string $class)
    {
        $this->rootEntity = $this->makeRootEntity($class);
    }

    /**
     * @param $class
     * @return mixed
     * @throws NonValidEntityException
     */
    protected function makeRootEntity($class)
    {
        if (! class_exists($class)) {
            throw new NonValidEntityException($class . ' not exists');
        }

        $instance = new $class;

        if (! $instance instanceof Model) {
            throw new NonValidEntityException($class . ' is not an instance of eloquent model');
        }

        return $instance;
    }

    /**
     * @param array $struct
     * @return $this
     */
    public function setStruct(array $struct)
    {
        $this->struct = $struct;

        if (array_get($struct, 'id', false) !== false) {
            $this->rootEntity = $this->rootEntity->find(array_get($struct, 'id'));
        }

        return $this;
    }

    /**
     * @return mixed
     */
    abstract function commit();

    abstract function findById($id);

    abstract function aggregate();

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        $allowed = array_merge($this->rootEntity->getFillable(), array_keys($this->rootEntity->getAttributes()));

        if (! is_array($allowed)) {
            return [];
        }

        $prepared = [];

        foreach ($allowed as $el) {
            $prepared[$el] = array_get($this->struct, $el);
        }

        return $prepared;
    }
}