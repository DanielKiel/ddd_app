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
abstract class DBRepository
{
    private $rootEntity;

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
     * @return mixed
     */
    abstract function commit();

    abstract function findById($id);
}