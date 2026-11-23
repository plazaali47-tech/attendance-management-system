<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('status', 10); // present, late, absent, excused
            $table->time('time_in')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Isa lang ka record kada estudyante kada adlaw
            $table->unique(['student_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
