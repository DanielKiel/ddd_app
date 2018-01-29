<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('task_id')->unsigned();
            $table->string('status')->default('open');
            $table->dateTime('scheduled');
            $table->integer('grading')->default(0); //"benotung"; 0 means that is not graded till yet
            $table->longText('content')->nullable();

            $table->timestamps();

            $table->index('task_id');
            $table->index('scheduled');
            $table->index('grading');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams');
    }
}
