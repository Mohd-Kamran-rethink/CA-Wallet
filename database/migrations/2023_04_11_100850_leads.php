<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Leads extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source_id');
            $table->string('name');
            $table->string('number');
            $table->string('agent_id');
            $table->string('manager_id');
            $table->string('lead_status_id')->nullable();
            $table->string('date')->nullable();
            $table->string('language')->nullable();
            $table->string('idName')->nullable();
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