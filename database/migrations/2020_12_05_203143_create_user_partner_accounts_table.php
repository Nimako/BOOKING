<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUserPartnerAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_partner_accounts', function (Blueprint $table) {
           $table->id();
           // Central Column
           $table->char('status', 2)->default(1);
           $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
           $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
           // Fields
           $table->uuid('uuid');
           $table->string('Fullname')->nullable();
           $table->string('phone_no')->nullable();
           $table->string('email')->unique();
           $table->string('password')->nullable();
           $table->string('useraccount_id')->nullable();
           $table->string('email_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_partner_accounts');
    }
}
