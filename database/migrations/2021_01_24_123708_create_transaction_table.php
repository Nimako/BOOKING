<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->uuid('uuid');
            $table->string('property_id',200);
            $table->string('unit_id',200);
            $table->string('channel')->nullable();
            $table->decimal('amount', $precision = 10, $scale = 2);
            $table->integer('quantity')->nullable();
            $table->integer('discount')->nullable();
            $table->decimal('tax', $precision = 10, $scale = 2)->nullable();
            $table->decimal('fee', $precision = 10, $scale = 2)->nullable();
            $table->string('transaction_id');
            $table->string('api_reference_id')->nullable();
            $table->text('api_response')->nullable();
            $table->enum('status', ['PROCESSING', 'WAITING','COMPLETE','DECLINE','CANCEL']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
