<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->timestamps();

            $table->index(['date', 'student_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};