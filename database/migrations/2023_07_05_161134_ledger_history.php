<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class LedgerHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ladger_histories', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('opening_balance')->nullable();
            $table->string('closing_balance')->nullable();
            $table->string('amount')->nullable();
            $table->string('remark')->nullable();
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
