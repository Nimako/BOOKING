<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApartmentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_details', function (Blueprint $table) {
           $table->id();
           $table->uuid('uuid');
           $table->timestamps();
           // Central Columns
           $table->char('status', 2)->default(1);
           $table->bigInteger('created_by')->nullable();
           $table->bigInteger('updated_by')->nullable();
           // Table Fields
           $table->unsignedBigInteger('property_id');
           $table->string('room_name')->nullable();
           $table->unsignedBigInteger('common_room_amenity_id')->nullable();
           $table->string('image_ids', 255)->nullable();
           $table->text('image_paths')->nullable();
           $table->integer('total_guest_capacity')->nullable();
           $table->integer('total_bathrooms')->nullable();
           $table->integer('num_of_rooms')->nullable();
           $table->string('price_list',255)->nullable();
           $table->integer('similiar_rooms')->default(0);
           // Foreign Keys
           //$table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apartment_details');
    }
}
