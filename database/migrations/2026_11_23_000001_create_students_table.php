<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_no', 30)->unique();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('course', 50);
            $table->unsignedTinyInteger('year_level');
            $table->string('section', 20);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
}
