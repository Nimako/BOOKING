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
           $table->uuid('uuid');
           $table->char('status', 2)->default(1);
           $table->bigInteger('booked_by')->nullable();
           // Table Fields
           $table->unsignedBigInteger('property_id');
           $table->unsignedBigInteger('property_details_id');
           $table->date('expected_checkin')->nullable();
           $table->date('expected_checkout')->nullable();
           $table->date('expected_numofguest')->nullable();
           $table->date('actual_checkin')->nullable();
           $table->date('actual_checkout')->nullable();
           $table->date('actual_numofguest')->nullable();
           $table->integer('num_of_rooms')->nullable()->default(1);
           $table->float('total_price')->nullable();
           $table->text('other_details')->nullable();
           $table->string('promo_code')->nullable();
           $table->float('discount_applied')->nullable();
           $table->tinyInteger('rescheduled')->nullable()->default(0);
           $table->integer('rescheduled_id')->nullable();
           $table->string('pin')->nullable();
           $table->string('number')->nullable();
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
