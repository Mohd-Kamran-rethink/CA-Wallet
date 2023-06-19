<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BankDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('holder_name')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('amount')->nullable();
            $table->enum('is_active',['Yes','No'])->default('Yes');
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
