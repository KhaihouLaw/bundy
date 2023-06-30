<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('approver');
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('id_code')->nullable()->default(null);
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->date('birthdate')->nullable();
            $table->bigInteger('department_id')->unsigned();
            $table->bigInteger('approver_id')->unsigned()->nullable()->default(null);
            $table->tinyInteger('sick_leave')->unsigned()->default(0);
            $table->tinyInteger('vacation_leave')->unsigned()->default(0);
            $table->timestamps();

            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->string('email')->unique();
            $table->bigInteger('employee_id')->nullable()->unsigned()->default(null);
            $table->string('google_token')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', [1, 0])->default('1');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
    }
}
