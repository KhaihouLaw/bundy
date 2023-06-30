<?php

use App\Models\Timesheet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvancePunchClocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advance_punch_clocks', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [Timesheet::TIME_IN, Timesheet::TIME_OUT]);
            $table->string('description')->nullable();
            $table->string('access_code')->unique();
            $table->json('schedules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_punch_clocks');
    }
}
