<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEmployeePositionStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('position', 100);
            $table->timestamps();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->bigInteger('position_id')->unsigned()->nullable()->default(null)->after('approver_id');
            $table->enum('employment_type', ["Regular", "Probationary", "Part Time"])->nullable()->default(null)->after('approver_id');

            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
            $table->dropColumn('employment_type');
        });

        Schema::dropIfExists('positions');
    }
}
