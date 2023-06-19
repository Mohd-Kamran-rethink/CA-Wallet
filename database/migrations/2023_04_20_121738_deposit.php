<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Deposit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id')->nullable();
            $table->string('exchange_id')->nullable();
            $table->string('bank_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('client_id')->nullable();
            $table->string('amount')->default(0);
            $table->string('bonus')->nullable();
            $table->string('opening_balance')->nullable();
            $table->enum('type',['deposit','redeposit','withdraw','withdraw_revert','deposit_revert'])->default('deposit');
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
