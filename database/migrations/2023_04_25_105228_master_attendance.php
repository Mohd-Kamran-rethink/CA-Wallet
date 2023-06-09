<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MasterAttendance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->date('date')->default(date('Y-m-d'));
            $table->string('hours');
            $table->string('breaktime')->default('00:00:00');
            $table->string('actions');
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
        //
    }
}
