<?php

namespace Tests\Feature\Core\Task;

use Carbon\Carbon;
use Core\Aggregates\Task\Methods\Commands\Homework\FetchByCriticalDeadlines;
use Tests\TestCase;

class HomeworkGetTest extends TestCase
{
    public function testGetCriticalHomeworks()
    {
        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        $method = app()->make('Core_Task_Methods_CreatingHomework');

        $critical = Carbon::now()->addDay(-5);
        $method->createAHomeworkSeriesByStudent($student->id, $subject->id, [
            [
                'deadline' => $critical,
                'todo' => 'Critical 1',
            ],
            [
                'deadline' => $critical,
                'todo' => 'Critical 2',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 1',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 2',
            ]
        ]);

        $method->createAHomeworkSeriesByStudent($student->id, $subject->id, [
            [
                'deadline' => $critical,
                'todo' => 'B-Critical 1',
            ],
            [
                'deadline' => $critical,
                'todo' => 'B-Critical 2',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 1',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 2',
            ]
        ]);

        //here no critical times will be
        $method->createAHomeworkSeriesByStudent($student->id, $subject->id, [
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 1',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Was 2',
            ]
        ]);

        /** @var FetchByCriticalDeadlines $method */
        $method = app()->make('Core_Task_Methods_FetchByCriticalDeadline');

        $result = $method->fetchByTimeExpression('addDay(-2)');

        $this->assertEquals(2, $result->count());
    }
}