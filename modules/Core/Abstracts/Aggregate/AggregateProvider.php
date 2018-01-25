<?php
/**
 * Created by PhpStorm.
 * User: dk
 * Date: 25.01.18
 * Time: 09:45
 */

namespace Core\Abstracts\Aggregate;


use Illuminate\Support\ServiceProvider;

abstract class AggregateProvider extends ServiceProvider
{
    abstract function boot();

    abstract function register();
}