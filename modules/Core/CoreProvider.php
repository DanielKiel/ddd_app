<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 10:31
 */

namespace Core;


use Core\Aggregates\SchoolClass\SchoolClassProvider;
use Core\Aggregates\Student\StudentProvider;
use Core\Aggregates\Subject\SubjectProvider;
use Illuminate\Support\ServiceProvider;

class CoreProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->register(StudentProvider::class);
        $this->app->register(SchoolClassProvider::class);
        $this->app->register(SubjectProvider::class);
    }
}