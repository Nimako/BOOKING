<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
           // Central Columns
           $table->char('status', 2)->default(1);
           $table->bigInteger('booked_by')->nullable();
           // Table Fields
           $table->unsignedBigInteger('property_id');
           $table->unsignedBigInteger('property_details_ids');
           $table->date('expected_checkin')->nullable();
           $table->date('expected_checkout')->nullable();
           $table->date('actual_checkin')->nullable();
           $table->date('actual_checkout')->nullable();
           $table->float('total_price')->nullable();
           $table->float('discount_given')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
