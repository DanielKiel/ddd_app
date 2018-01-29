<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 10:30
 */

namespace Core\Aggregates\Student;


use Core\Abstracts\Aggregate\AggregateProvider;
use Core\Aggregates\Student\Methods\Commands\HandlingSchoolClassRelations;
use Core\Aggregates\Student\Methods\Repositories\DBRepository;
use Core\Aggregates\Student\Structs\Entities\Student;

class StudentProvider extends AggregateProvider
{
    public function boot()
    {
        $this->app->bind('Core_Student_DBRepo',function() {
            return new DBRepository(Student::class);
        });
    }

    public function register()
    {
        // TODO: Implement register() method.
    }
}