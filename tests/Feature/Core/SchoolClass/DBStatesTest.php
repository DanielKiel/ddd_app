<?php

namespace Tests\Feature\Core\SchoolClass;


use Core\Aggregates\SchoolClass\Events\SchoolClassGotANewSubject;
use Core\Aggregates\SchoolClass\Events\SchoolClassWasCreated;
use Core\Aggregates\SchoolClass\Events\SchoolClassWasUpdated;
use Core\Aggregates\schoolClass\Methods\Repositories\DBRepository;
use Core\Aggregates\SchoolClass\Structs\SchoolClass;
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

    public function testSchoolClassGotANewSubject()
    {
        Event::fake();

        $repo = $this->app->make('Core_SchoolClass_DBRepo');

        $struct = [
            'name' => '5a'
        ];

        /** @var SchoolClass $schoolClass */
        $schoolClass = $repo->setStruct($struct)->commit();

        $subject =  $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => 'Englisch'])->commit();

        $schoolClass->assignANewSubject($subject);

        Event::assertDispatched(SchoolClassGotANewSubject::class, function ($e) use($subject) {
            return $e->subject->id === $subject->id;
        });

        $this->assertEquals(1, count($schoolClass->subjects));

        // a relation may not have be assigned twice!
        $schoolClass->assignANewSubject($subject);

        $this->assertEquals(1, count($schoolClass->subjects));

        $this->assertEquals(
            1,
            \Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass::find($schoolClass->id)->subjects()->count()
        );

//        $student->leavesASchoolClass($schoolClass);
//
//        Event::assertDispatched(StudentHadLeavedASchoolClass::class, function ($e) use($schoolClass) {
//            return $e->schoolClass->id === $schoolClass->id;
//        });
    }
}