<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateInTimesheetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('timesheets', function (Blueprint $table) {
        //     $table->date('timesheet_date')->nullable()->default(null)->after('employee_id');
        // });
        // Schema::table('timesheet_modification_requests', function (Blueprint $table) {
        //     $table->date('timesheet_date')->nullable()->default(null)->after('employee_id');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('timesheets', function (Blueprint $table) {
        //     $table->dropColumn('timesheet_date');
        // });
        // Schema::table('timesheet_modification_requests', function (Blueprint $table) {
        //     $table->dropColumn('timesheet_date');
        // });
    }
}
