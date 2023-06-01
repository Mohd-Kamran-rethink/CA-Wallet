<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Transactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('client_id')->nullable();
            $table->string('amount');
            $table->string('bonus')->nullable();
            $table->string('total');
            $table->string('date');
            $table->string('utr_no')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('deposit_banker_id')->nullable();
            $table->string('depositer_id')->nullable();
            // for withdrawl
            $table->string('withdrawal_banker_id')->nullable();
            $table->string('withdrawrer_id')->nullable();
            $table->string('cancel_note')->nullable();
            $table->string('customer_bank_id')->nullable();
            $table->enum('type',['Deposit','Withdraw']);
            $table->enum('status',['Pending','Approve','Cancel']);
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
