<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolEventsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();
            $table->tinyInteger('semester')->default(1);
            $table->year('start_year');
            $table->year('end_year');
            $table->timestamps();
        });
        
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('holiday');
            $table->tinyInteger('month')->unsigned();
            $table->tinyInteger('day')->unsigned();
            $table->enum('type', ['Regular', 'Special Non-Working', 'Religious']);
            $table->tinyInteger('working_day')->unsigned()->default(0);
            $table->timestamps();
        });

        Schema::create('school_events', function (Blueprint $table) {
            $table->id();
            $table->string('event');
            $table->date('date')->nullable()->default(null);
            $table->bigInteger('academic_year_id')->unsigned();
            $table->tinyInteger('working_day')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school_events');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('academic_years');
    }
}
