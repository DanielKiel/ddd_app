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
use Core\Aggregates\Task\Methods\Commands\Homework\FetchByDeadlines;
use Core\Aggregates\Task\Methods\Commands\Homework\FinishingHomework;
use Core\Aggregates\Task\Methods\Commands\LearningUnit\CreatingLearningUnit;
use Core\Aggregates\Task\Methods\Commands\LearningUnit\FinishingLearningUnit;
use Core\Aggregates\Task\Methods\Exam\CreatingExam;
use Core\Aggregates\Task\Methods\Exam\FinishingExam;
use Core\Aggregates\Task\Methods\Repositories\TaskDBRepository;
use Core\Aggregates\Task\Methods\Repositories\TaskStatusRepository;
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

        $this->app->bind('Core_Task_Methods_FinishingHomework', function() {
            return new FinishingHomework();
        });

        $this->app->bind('Core_Task_Methods_FetchByDeadline', function() {
            return new FetchByDeadlines();
        });

        $this->app->bind('Core_Task_Methods_CreatingLearningUnit', function() {
            return new CreatingLearningUnit();
        });

        $this->app->bind('Core_Task_Methods_FinishingLearningUnit', function() {
            return new FinishingLearningUnit();
        });

        $this->app->bind('Core_Task_Methods_CreatingExam', function() {
            return new CreatingExam();
        });

        $this->app->bind('Core_Task_Methods_FinishingExam', function() {
            return new FinishingExam();
        });


        $this->app->events->subscribe(new TaskStatusRepository());
    }

    public function register()
    {
        // TODO: Implement register() method.
    }
}