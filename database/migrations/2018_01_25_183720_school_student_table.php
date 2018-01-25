<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SchoolStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_class_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_class_id')->unsigned();
            $table->bigInteger('student_id')->unsigned();
            $table->timestamps();

            $table->index(['school_class_id', 'student_id']);
            $table->index('student_id');
            $table->index('school_class_id');

            $table->foreign('school_class_id')->on('school_classes')->references('id');
            $table->foreign('student_id')->on('students')->references('id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_class_student', function (Blueprint $table) {
            $table->dropForeign('school_class_student_school_class_id_foreign');
            $table->dropForeign('school_class_student_student_id_foreign');
        });

        Schema::dropIfExists('school_class_student');
    }
}
