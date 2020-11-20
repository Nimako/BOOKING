<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUseraccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('useraccount', function (Blueprint $table) {
            $table->id();
            $table->dateTime('DateCreated')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('DateUpdated')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
           // Central Column
           $table->char('status', 2)->default(1);
           $table->bigInteger('created_by')->nullable();
           $table->bigInteger('updated_by')->nullable();
           // Fields
           $table->string('FirstName')->nullable();
           $table->string('LastName')->nullable();
           $table->string('Email')->unique();
           $table->string('PhoneNum')->nullable();
           $table->string('UserPassword')->nullable();
           $table->string('DisplayName')->nullable();
           $table->string('DateBirth')->nullable();
           $table->string('FireBaseUserID')->nullable();
           $table->string('FireBaseKey')->nullable();
           $table->string('Country')->nullable();
           $table->string('City')->nullable();
           $table->string('Region')->nullable();
           $table->string('ProfileImage')->nullable();
           $table->string('Provider')->nullable();
           $table->enum('HasProperty',array('YES','NO'))->default('No');
           $table->enum('Verified',array('YES','NO'))->default('No');
           $table->dateTime('FirstLogin')->nullable();
           $table->dateTime('LastLogin')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('useraccount');
    }
}
