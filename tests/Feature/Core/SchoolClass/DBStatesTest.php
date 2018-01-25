<?php

namespace Tests\Feature\Core\SchoolClass;


use Core\Aggregates\SchoolClass\Events\SchoolClassWasCreated;
use Core\Aggregates\SchoolClass\Events\SchoolClassWasUpdated;
use Core\Aggregates\schoolClass\Methods\Repositories\DBRepository;
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
        $this->assertInstanceOf(DBRepository::class, $this->app->make('Core_SchoolClass_DBRepo'));
    }

    public function testCreateAndUpdate()
    {
        Event::fake();

        $repo = $this->app->make('Core_SchoolClass_DBRepo');

        $struct = [
            'name' => '4a'
        ];

        $result = $repo->setStruct($struct)->commit();

        Event::assertDispatched(SchoolClassWasCreated::class, function ($e) use ($result) {
            return $e->schoolClass->id === $result->id;
        });

        $update = $result->toArray();
        $update['name'] = '5a';

        //again and check if update method do not dispatch event again, instead dispatching update event
        Event::fake();

        $updated = $repo->setStruct($update)->commit();

        Event::assertNotDispatched(SchoolClassWasCreated::class);

        Event::assertDispatched(SchoolClassWasUpdated::class, function ($e) {
            return $e->schoolClass->name === '5a';
        });

        $this->assertEquals($result->id, $updated->id);
        $this->assertEquals('5a', $updated->name);
    }
}