<?php

namespace Tests\Feature\Core\Task;

use Carbon\Carbon;
use Core\Aggregates\Task\Events\Homework\AHomeworkSerieWasCreated;
use Core\Aggregates\Task\Events\Homework\ASingleHomeworkWasCreated;
use Core\Aggregates\Task\Events\TaskWasCreated;
use Core\Aggregates\Task\Events\TaskWasUpdated;
use Core\Aggregates\Task\Methods\Commands\Homework\CreatingHomework;
use Core\Aggregates\Task\Methods\Repositories\TaskDBRepository;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DBStatesTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBinding()
    {
        $this->assertInstanceOf(TaskDBRepository::class, $this->app->make('Core_Task_DBRepo'));
    }

    public function testCreateAndUpdate()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        $repo = $this->app->make('Core_Task_DBRepo');

        $struct = [
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'status' => 'open'
        ];

        $result = $repo->setStruct($struct)->commit();

        Event::assertDispatched(TaskWasCreated::class, function ($e) use ($result) {
            return $e->task->id === $result->id;
        });

        $update = $result->toArray();
        $update['status'] = 'closed';

        //again and check if update method do not dispatch event again, instead dispatching update event
        Event::fake();

        $updated = $repo->setStruct($update)->commit();

        Event::assertNotDispatched(TaskWasCreated::class);

        Event::assertDispatched(TaskWasUpdated::class, function ($e) {
            return $e->task->status === 'closed';
        });

        $this->assertEquals($result->id, $updated->id);
        $this->assertEquals('closed', $updated->status);
    }

    public function testCreateSingleHomework()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingHomework $method */
        $method = app()->make('Core_Task_Methods_CreatingHomework');

        $task = $method->createASingleHomeworkByStudent($student->id, $subject->id, [
            'deadline' => Carbon::now()->addDay(5),
            'todo' => 'Mache deine Arbeit',
        ]);

        Event::assertDispatched(ASingleHomeworkWasCreated::class, function ($e) {
            return array_first($e->task->homework)['todo'] = 'Mache deine Arbeit';
        });

        $this->assertEquals('open', $task->status);


    }

    public function testCreateHomeworkSerie()
    {
        Event::fake();

        $student = $this->app->make('Core_Student_DBRepo')->setStruct([])->commit();
        $subject = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => '5a'])->commit();

        /** @var CreatingHomework $method */
        $method = app()->make('Core_Task_Methods_CreatingHomework');

        $task = $method->createAHomeworkSeriesByStudent($student->id, $subject->id, [
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Mache deine Arbeit',
            ],
            [
                'deadline' => Carbon::now()->addDay(5),
                'todo' => 'Ich bin Nummer 2',
            ]
        ]);

        Event::assertDispatched(AHomeworkSerieWasCreated::class, function ($e) {
            return array_last($e->task->homework)['todo'] = 'Ich bin Nummer 2';
        });

        $this->assertEquals('open', $task->status);


    }
}
