<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 26.01.18
 * Time: 14:39
 */

namespace Core\Aggregates\Task;


use Core\Abstracts\Aggregate\AggregateProvider;
use Core\Aggregates\Task\Methods\Commands\Homework\CreatingHomework;
use Core\Aggregates\Task\Methods\Repositories\TaskDBRepository;
use Core\Aggregates\Task\Structs\Entities\Task;

class TaskProvider extends AggregateProvider
{
    public function boot()
    {
        $this->app->bind('Core_Task_DBRepo', function(){
            return new TaskDBRepository(Task::class);
        });

        $this->app->bind('Core_Task_Methods_CreatingHomework', function() {
            return new CreatingHomework();
        });
    }

    public function register()
    {
        // TODO: Implement register() method.
    }
}