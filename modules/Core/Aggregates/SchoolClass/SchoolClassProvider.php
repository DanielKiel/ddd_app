<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 15:25
 */

namespace Core\Aggregates\SchoolClass;


use Core\Abstracts\Aggregate\AggregateProvider;
use Core\Aggregates\SchoolClass\Methods\Repositories\DBRepository;
use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;

class SchoolClassProvider extends AggregateProvider
{
    public function boot()
    {
        $this->app->bind('Core_SchoolClass_DBRepo', function() {
            return new DBRepository(SchoolClass::class);
        });
    }

    public function register()
    {
        // TODO: Implement register() method.
    }
}