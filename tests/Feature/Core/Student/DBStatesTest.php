<?php

namespace Tests\Feature\Core\Student;

use Core\Aggregates\SchoolClass\Structs\SchoolClass;
use Core\Aggregates\Student\Events\StudentHadJoinedASchoolClass;
use Core\Aggregates\Student\Events\StudentHadLeavedASchoolClass;
use Core\Aggregates\Student\Events\StudentWasCreated;
use Core\Aggregates\Student\Events\StudentWasUpdated;
use Core\Aggregates\Student\Methods\Repositories\DBRepository;
use Core\Aggregates\Student\Structs\Student;
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
        $this->assertInstanceOf(DBRepository::class, $this->app->make('Core_Student_DBRepo'));
    }

    public function testCreateAndUpdate()
    {
        Event::fake();

        $repo = $this->app->make('Core_Student_DBRepo');

        $struct = [
            'account' => [
                'name' => 'me',
                'password' => 'me',
                'email' => 'mail@mailmeagain.develop'
            ]
        ];

        $result = $repo->setStruct($struct)->commit();

        Event::assertDispatched(StudentWasCreated::class, function ($e) use ($result) {
            return $e->student->id === $result->id;
        });

        $update = $result->toArray();
        $update['account']['email'] = 'again@again';

        //again and check if update method do not dispatch event again, instead dispatching update event
        Event::fake();

        $updated = $repo->setStruct($update)->commit();

        Event::assertNotDispatched(StudentWasCreated::class);

        Event::assertDispatched(StudentWasUpdated::class, function ($e) {
            return $e->student->account['email'] === 'again@again';
        });

        $this->assertEquals($result->id, $updated->id);
        $this->assertEquals($result->account['id'], $updated->account['id']);
        $this->assertEquals('again@again', $updated->account['email']);
    }

    public function testJoiningASchoolClass()
    {
        Event::fake();

        $repo = $this->app->make('Core_Student_DBRepo');

        $struct = [
            'account' => [
                'name' => 'me',
                'password' => 'me',
                'email' => 'mail@mailmeagain.develop'
            ]
        ];

        /** @var Student $student */
        $student = $repo->setStruct($struct)->commit();

        $schoolClass =  $this->app->make('Core_SchoolClass_DBRepo')->setStruct(['name' => '5a'])->commit();

        $student->joinsASchoolClass($schoolClass);

        Event::assertDispatched(StudentHadJoinedASchoolClass::class, function ($e) use($schoolClass) {
            return $e->schoolClass->id === $schoolClass->id;
        });

        $this->assertEquals(1, count($student->classes));

        // a relation may not have be assigned twice!
        $student->joinsASchoolClass($schoolClass);

        $this->assertEquals(1, count($student->classes));

        $this->assertEquals(
            1,
            \Core\Aggregates\Student\Structs\Entities\Student::find($student->id)->classes()->count()
        );

        $student->leavesASchoolClass($schoolClass);

        Event::assertDispatched(StudentHadLeavedASchoolClass::class, function ($e) use($schoolClass) {
            return $e->schoolClass->id === $schoolClass->id;
        });

        $this->assertEquals(
            0,
            \Core\Aggregates\Student\Structs\Entities\Student::find($student->id)->classes()->count()
        );

    }

    public function testSubjectAggregationAtJoinedSchoolClasses()
    {
        Event::fake();

        $repo = $this->app->make('Core_Student_DBRepo');

        $struct = [
            'account' => [
                'name' => 'me',
                'password' => 'me',
                'email' => 'mail@mailmeagain.develop'
            ]
        ];

        /** @var Student $student */
        $student = $repo->setStruct($struct)->commit();

        /** @var SchoolClass $schoolClass */
        $schoolClass =  $this->app->make('Core_SchoolClass_DBRepo')->setStruct(['name' => '5a'])->commit();

        $subjectGerman = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => 'Deutsch'])->commit();
        $subjectEnglish = $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => 'Englisch'])->commit();
        $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => 'Mathe'])->commit();
        $this->app->make('Core_Subject_DBRepo')->setStruct(['name' => 'Nons'])->commit();

        $schoolClass->assignANewSubject($subjectGerman);
        $schoolClass->assignANewSubject($subjectEnglish);

        $student->joinsASchoolClass($schoolClass);

        $this->assertEquals(2, count($student->subjects));

        foreach ($student->subjects as $subject) {
            $this->assertNotEquals('Mathe', $subject->name);
            $this->assertNotEquals('Nons', $subject->name);
        }
    }
}
