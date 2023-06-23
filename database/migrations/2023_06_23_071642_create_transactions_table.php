<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->bigInteger('purchase_code_id')->unsigned();
            $table->bigInteger('package_module_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->integer('amount');
            $table->string('ref');
            $table->date('expiry_date')->nullable();
            $table->foreign('purchase_code_id')->references('id')->on('purchase_codes')->onDelete('cascade');
            $table->foreign('package_module_id')->references('id')->on('package_modules')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('transactions');
    }
};
