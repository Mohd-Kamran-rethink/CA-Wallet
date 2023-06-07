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
            $table->string('name')->nullable();
            $table->string('number');
            $table->string('agent_id')->nullable();
            $table->string('manager_id')->nullable();
            $table->string('date')->nullable();
            $table->string('language')->nullable();
            $table->string('state')->nullable();
            $table->string('zone')->nullable();
            $table->string('idName')->nullable();
            $table->string('current_status')->nullable();
            $table->string('status_id')->default('null');
            $table->string('remark')->nullable();
            $table->string('followup_date')->nullable();
            $table->string('transfered_language')->nullable();
            $table->string('amount')->nullable();
            $table->string('leads_date')->nullable();
            $table->enum('is_approved',['No','Yes']);
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
