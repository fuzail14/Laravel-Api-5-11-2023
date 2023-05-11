<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

       
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->decimal('charges');
            $table->decimal('chargesafterduedate');
            $table->decimal('appcharges');
            $table->decimal('tax');
            $table->decimal('balance');
            $table->decimal('payableamount');
            $table->unsignedBigInteger('subadminid');
            $table->foreign('subadminid')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('residentid');
            // $table->foreign('residentid')->references('id')->on('residents')->onDelete('cascade');
            $table->unsignedBigInteger('propertyid');
            $table->foreign('propertyid')->references('id')->on('properties')->onDelete('cascade');
            $table->unsignedBigInteger('measurementid');
            $table->foreign('measurementid')->references('id')->on('measurements')->onDelete('cascade');
            $table->date('duedate');
            $table->date('billstartdate');
            $table->date('billenddate');
            $table->string('month');
            $table->integer('status');
            $table->integer('noofappusers');
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
        Schema::dropIfExists('bills');
    }
}
