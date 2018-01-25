<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 10:34
 */
namespace Core\Abstracts\Aggregate\Contracts\Repositories;


interface AbstractDBRepositoryInterface
{
    public function commit();

    public function findById($id);
}