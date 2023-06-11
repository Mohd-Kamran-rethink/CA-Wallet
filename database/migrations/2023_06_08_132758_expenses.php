<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Expenses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('main_type');
            $table->string('transfer_type')->nullable();
            $table->string('accounting_type')->nullable();
            $table->string('creditor_id')->nullable();
            $table->string('sender_bank')->nullable();
            $table->string('receiver_bank')->nullable();
            $table->string('department_id')->nullable();
            $table->string('expense_type_id')->nullable();
            $table->enum('transaction_type',['Cash','Bank'])->nullable();
            $table->string('bank_id')->nullable();
            $table->string('currency_type')->nullable();
            $table->string('currency_rate')->nullable();
            $table->string('amount');
            $table->string('remark')->nullable();
            $table->string('attatchement')->nullable();
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
