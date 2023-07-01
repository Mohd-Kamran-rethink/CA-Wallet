<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientsMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('agent_id')->nullable();
            $table->string('exchange_id')->nullable();
            $table->string('number');
            $table->string('ca_id')->nullable();
            $table->bigInteger('deposit_amount')->nullable();
            $table->string('date')->nullable();
            $table->enum('is_number_reveal',['Yes','No'])->default('No');
            $table->enum('isDeleted',['Yes','No'])->default('No');
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
