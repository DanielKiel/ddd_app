<?php

namespace Tests\Feature\Core\Task;

use Carbon\Carbon;
use Core\Aggregates\Task\Events\LearningUnit\ALearningUnitSerieWasCreatedByAStudent;
use Core\Aggregates\Task\Events\LearningUnit\ASingleLearningUnitWasCreatedByAStudent;
use Core\Aggregates\Task\Methods\Commands\LearningUnit\CreatingLearningUnit;
use Core\Aggregates\Task\Methods\Commands\LearningUnit\FinishingLearningUnit;
use Core\Aggregates\Task\Structs\Entities\Task;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LearningUnitDBTest extends TestCase
{
    public function testCreateSingleLearningUnit()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_CreatingLearningUnit');

        $task = $method->createASingleLearningUnitByStudent($student->id, $subject->id, [
            'scheduled' => Carbon::now()->addDay(5),
            'todo' => 'Mache deine Arbeit',
        ]);

        Event::assertDispatched(ASingleLearningUnitWasCreatedByAStudent::class, function ($e) {
            return array_first($e->task->homework)['todo'] = 'Mache deine Arbeit';
        });

        $this->assertEquals('open', $task->status);
    }

    public function testFinishLearningUnit()
    {
        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_CreatingLearningUnit');

        $task = $method->createASingleLearningUnitByStudent($student->id, $subject->id, [
            'scheduled' => Carbon::now()->addDay(5),
            'todo' => 'Mache deine Arbeit',
        ]);

        //now finish  homework!
        /** @var FinishingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_FinishingLearningUnit');

        //cause we internally work with events, the taks will be closed by a subscriber repository
        $task = $method->finishingLearningUnitByAStudent(array_first($task->learning_units)['id'], $student);
        $this->assertEquals('closed', $task->status);

        //check the entitiy
        $this->assertEquals('closed', Task::find($task->id)->status);
    }

    public function testCreateLearningUnitSerie()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_CreatingLearningUnit');

        $task = $method->createALearningUnitSeriesByStudent($student->id, $subject->id, [
            [
                'scheduled' => Carbon::now()->addDay(5),
                'todo' => 'Mache deine Arbeit',
            ],
            [
                'scheduled' => Carbon::now()->addDay(5),
                'todo' => 'Lerne was',
            ]
        ]);

        Event::assertDispatched(ALearningUnitSerieWasCreatedByAStudent::class, function ($e) {
            return array_first($e->task->learningUnit)['todo'] = 'Mache deine Arbeit';
        });

        $this->assertEquals('open', $task->status);
    }

    public function testFinishLearningUnitSerie()
    {
        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_CreatingLearningUnit');

        $task = $method->createALearningUnitSeriesByStudent($student->id, $subject->id, [
            [
                'scheduled' => Carbon::now()->addDay(5),
                'todo' => 'Mache deine Arbeit',
            ],
            [
                'scheduled' => Carbon::now()->addDay(5),
                'todo' => 'Lerne was',
            ]
        ]);

        //now finish  homework!
        /** @var FinishingLearningUnit $method */
        $method = app()->make('Core_Task_Methods_FinishingLearningUnit');

        //cause we internally work with events, the taks will be handled by a subscriber repository
        //cause there are multiple homework opened at the task, task can only be closed when all homework are closed, so test it
        $task = $method->finishingLearningUnitByAStudent(array_first($task->learning_units)['id'], $student);
        $this->assertEquals('open', $task->status);

        //check the entitiy
        $this->assertEquals('open', Task::find($task->id)->status);

        $task = $method->finishingLearningUnitByAStudent(array_last($task->learning_units)['id'], $student);
        $this->assertEquals('closed', $task->status);

        //check the entitiy
        $this->assertEquals('closed', Task::find($task->id)->status);
    }
}
