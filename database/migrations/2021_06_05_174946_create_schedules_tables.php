<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->bigInteger('academic_year_id')->unsigned();
            $table->string('period')->default('1st semester');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_schedule_id')->unsigned();
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->foreign('employee_schedule_id')->references('id')->on('employee_schedules')->onDelete('cascade');
        });

        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->date('timesheet_date')->nullable()->default(null);
            $table->bigInteger('employee_schedule_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('schedule_id')->unsigned();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->time('overtime_start')->nullable();
            $table->time('overtime_end')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
            $table->foreign('employee_schedule_id')->references('id')->on('employee_schedules')->onDelete('cascade');
            $table->index(['employee_id', 'timesheet_date']);
        });

        Schema::create('timesheet_modification_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('employee_id')->unsigned();
            $table->date('timesheet_date')->nullable()->default(null);
            $table->bigInteger('employee_schedule_id')->unsigned()->nullable()->default(null);
            $table->bigInteger('timesheet_id')->unsigned();
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->time('overtime_start')->nullable();
            $table->time('overtime_end')->nullable();
            $table->enum('status', ['pending', 'rejected', 'approved', 'cancelled'])->default('pending');
            $table->bigInteger('reviewed_by')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('timesheet_id')->references('id')->on('timesheets')->onDelete('cascade');
            $table->foreign('employee_schedule_id')->references('id')->on('employee_schedules')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['employee_id', 'timesheet_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_modification_requests');
        Schema::dropIfExists('timesheets');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('employee_schedules');
    }
}
