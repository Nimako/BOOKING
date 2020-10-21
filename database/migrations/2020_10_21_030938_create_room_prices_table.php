<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Table Fields
            $table->unsignedBigInteger('room_id');
            $table->string('guest_occupancy',255);
            $table->float('discount')->nullable();
            $table->float('amount')->nullable();
            // Foreign Keys
            $table->foreign('room_id')->references('id')->on('room_apartments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_prices');
    }
}
