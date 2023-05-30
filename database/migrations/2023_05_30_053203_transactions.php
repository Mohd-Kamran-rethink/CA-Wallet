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
            $table->string('amount');
            $table->string('bonus');
            $table->string('total');
            $table->string('date');
            $table->string('utr_no');
            $table->string('bank_account');
            $table->string('deposit_banker_id');
            $table->string('depositer_id')->nullable();
            $table->string('cancel_note')->nullable();
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
