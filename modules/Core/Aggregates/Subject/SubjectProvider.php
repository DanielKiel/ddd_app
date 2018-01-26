<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 08:53
 */

namespace Core\Aggregates\Subject;


use Core\Abstracts\Aggregate\AggregateProvider;
use Core\Aggregates\Subject\Methods\Repositories\DBRepository;
use Core\Aggregates\Subject\Structs\Entities\Subject;

class SubjectProvider extends AggregateProvider
{
    public function boot()
    {
        $this->app->bind('Core_Subject_DBRepo', function(){
            return new DBRepository(Subject::class);
        });
    }

    public function register()
    {
        // TODO: Implement register() method.
    }
}