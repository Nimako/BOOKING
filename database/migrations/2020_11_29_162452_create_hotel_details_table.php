<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
           // Central Columns
           $table->char('status', 2)->default(1);
           $table->bigInteger('created_by')->nullable();
           $table->bigInteger('updated_by')->nullable();
           // Table Fields
           $table->unsignedBigInteger('property_id');
           $table->string('room_name');
           $table->text('bed_types')->nullable();
           $table->text('added_amenities')->nullable();
           $table->string('dimension')->nullable();
           $table->text('image_paths')->nullable();
           $table->text('prices')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_details');
    }
}