<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LeadStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('status_id');
            $table->string('remark')->nullable();
            $table->string('followup_date')->nullable();
            $table->string('amount')->nullable();
            $table->string('lead_id');
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
