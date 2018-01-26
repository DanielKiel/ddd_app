<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SchoolClassSubject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_class_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('school_class_id')->unsigned();
            $table->bigInteger('subject_id')->unsigned();
            $table->timestamps();

            $table->index(['school_class_id', 'subject_id']);
            $table->index('subject_id');
            $table->index('school_class_id');

            $table->foreign('school_class_id')->on('school_classes')->references('id');
            $table->foreign('subject_id')->on('subjects')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('school_class_subject', function (Blueprint $table) {
            $table->dropForeign('school_class_subject_school_class_id_foreign');
            $table->dropForeign('school_class_subject_subject_id_foreign');
        });

        Schema::dropIfExists('school_class_subject');
    }
}
