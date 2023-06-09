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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('password')->nullable();
            $table->string('role');
            $table->string('franchise_id')->nullable();
            $table->string('language')->nullable();
            $table->string('zone')->nullable();
            $table->string('state')->nullable();
            $table->string('agent_type')->nullable();
            $table->string('lead_type')->nullable();
            $table->string('assigned_department')->nullable();
            $table->string('assigned_numbers')->nullable();
            $table->string('platform')->nullable();
            $table->enum('is_admin',['Yes','No'])->default('No');
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
        Schema::dropIfExists('users');
    }
}
