<?php

namespace Tests\Feature\Core\Task;

use Carbon\Carbon;
use Core\Aggregates\Task\Events\Exam\AnExamSeriesWasCreatedByStudent;
use Core\Aggregates\Task\Events\Exam\AnExamWasCreatedByStudent;
use Core\Aggregates\Task\Methods\Commands\Exam\CreatingExam;
use Core\Aggregates\Task\Methods\Commands\Exam\FinishingExam;
use Core\Aggregates\Task\Structs\Entities\Task;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamDBTest extends TestCase
{
    public function testCreateSingleExam()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingExam $method */
        $method = app()->make('Core_Task_Methods_CreatingExam');

        $task = $method->createAnExamByStudent($student->id, $subject->id, [
            'scheduled' => Carbon::now()->addDay(5),
            'content' => 'Mache deine Arbeit',
        ]);

        Event::assertDispatched(AnExamWasCreatedByStudent::class, function ($e) {
            return array_first($e->task->exams)['content'] = 'Mache deine Arbeit';
        });

        $this->assertEquals('open', $task->status);
    }

    public function testFinishLearningUnit()
    {
        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingExam $method */
        $method = app()->make('Core_Task_Methods_CreatingExam');

        $task = $method->createAnExamByStudent($student->id, $subject->id, [
            'scheduled' => Carbon::now()->addDay(5),
            'content' => 'Mache deine Arbeit',
        ]);

        //now finish  homework!
        /** @var FinishingExam $method */
        $method = app()->make('Core_Task_Methods_FinishingExam');

        //cause we internally work with events, the taks will be closed by a subscriber repository
        $task = $method->finishingExamByAStudent(array_first($task->exams)['id'], $student);
        $this->assertEquals('closed', $task->status);

        //check the entitiy
        $this->assertEquals('closed', Task::find($task->id)->status);
    }

    public function testCreateExamSerie()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingExam $method */
        $method = app()->make('Core_Task_Methods_CreatingExam');

        $task = $method->createAnExamSeriesByStudent($student->id, $subject->id, [
            [
                'scheduled' => Carbon::now()->addDay(5),
                'content' => 'Mache deine Arbeit',
            ],
            [
                'scheduled' => Carbon::now()->addDay(5),
                'content' => 'Lerne was',
            ]
        ]);

        Event::assertDispatched(AnExamSeriesWasCreatedByStudent::class, function ($e) {
            return array_first($e->task->learningUnit)['content'] = 'Mache deine Arbeit';
        });

        $this->assertEquals('open', $task->status);
    }

    public function testFinishExamSerie()
    {
        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingExam $method */
        $method = app()->make('Core_Task_Methods_CreatingExam');

        $task = $method->createAnExamSeriesByStudent($student->id, $subject->id, [
            [
                'scheduled' => Carbon::now()->addDay(5),
                'content' => 'Mache deine Arbeit',
            ],
            [
                'scheduled' => Carbon::now()->addDay(5),
                'content' => 'Lerne was',
            ]
        ]);

        //now finish  homework!
        /** @var FinishingExam $method */
        $method = app()->make('Core_Task_Methods_FinishingExam');

        //cause we internally work with events, the taks will be handled by a subscriber repository
        //cause there are multiple homework opened at the task, task can only be closed when all homework are closed, so test it
        $task = $method->finishingExamByAStudent(array_first($task->exams)['id'], $student);
        $this->assertEquals('open', $task->status);

        //check the entitiy
        $this->assertEquals('open', Task::find($task->id)->status);

        $task = $method->finishingExamByAStudent(array_last($task->exams)['id'], $student);
        $this->assertEquals('closed', $task->status);

        //check the entitiy
        $this->assertEquals('closed', Task::find($task->id)->status);
    }
}
