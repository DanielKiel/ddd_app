<?php

namespace Tests\Feature\Core\Subject;

use Core\Aggregates\Subject\Events\SubjectWasCreated;
use Core\Aggregates\Subject\Events\SubjectWasUpdated;
use Core\Aggregates\Subject\Methods\Repositories\DBRepository;
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
        $this->assertInstanceOf(DBRepository::class, $this->app->make('Core_Subject_DBRepo'));
    }

    public function testCreateAndUpdate()
    {
        Event::fake();

        $repo = $this->app->make('Core_Subject_DBRepo');

        $struct = [
            'name' => 'Englisch'
        ];

        $result = $repo->setStruct($struct)->commit();

        Event::assertDispatched(SubjectWasCreated::class, function ($e) use ($result) {
            return $e->subject->id === $result->id;
        });

        $update = $result->toArray();
        $update['name'] = 'Deutsch';

        //again and check if update method do not dispatch event again, instead dispatching update event
        Event::fake();

        $updated = $repo->setStruct($update)->commit();

        Event::assertNotDispatched(SubjectWasCreated::class);

        Event::assertDispatched(SubjectWasUpdated::class, function ($e) {
            return $e->subject->name === 'Deutsch';
        });

        $this->assertEquals($result->id, $updated->id);
        $this->assertEquals('Deutsch', $updated->name);
    }
}
