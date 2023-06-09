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
            $table->string('user_id');
            $table->enum('type',['Debit','Credit']);
            $table->string('department_id');
            $table->string('debiter_id');
            $table->string('expense_type_id');
            $table->enum('transaction_type',['Cash','Bank']);
            $table->string('bank_id');
            $table->string('currency_type');
            $table->string('amount');
            $table->string('remark');
            $table->string('attatchement');
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
