<?php

namespace Tests\Feature\Core\Raw;

use Core\Aggregates\SchoolClass\Structs\Entities\SchoolClass;
use Core\Aggregates\Student\Structs\Entities\Student;
use Core\Aggregates\Subject\Structs\Entities\Subject;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HasManyThroughTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testHasManyThroughForStudents()
    {
        $student = Student::create([]);
        $schoolClass = SchoolClass::create(['name' => '5']);
        $subject = Subject::create(['name' => 'Deutsch']);

        //do some irrelevant data to check queries
        $a1 = Subject::create(['name' => 'Nons']);
        $a2 = Subject::create(['name' => 'Quatsch']);
        $b1 = SchoolClass::create(['name' => '1']);
        $b2 = SchoolClass::create(['name' => '2']);
        $b1->subjects()->saveMany([$a1, $a2]);
        $b2->subjects()->saveMany([$a1, $a2]);
        //end dummy data

        $schoolClass->subjects()->save($subject);
        $student->classes()->save($schoolClass);

        $this->assertEquals(1, $student->subjects()->count());
        $this->assertEquals('Deutsch', $student->subjects()->first()->name);
    }
}
