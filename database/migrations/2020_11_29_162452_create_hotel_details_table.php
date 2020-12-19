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
           $table->string('own_multiple_hotel')->nullable();
           $table->string('name_of_company_group_chain')->nullable();
           $table->string('use_channel_manager')->nullable();
           $table->string('channel_manager_name')->nullable();
           $table->string('room_name')->nullable();
           $table->string('custom_room_name')->nullable();
           $table->string('listed_on')->nullable();
           $table->string('star_rating')->nullable();
           $table->text('smoking_policy')->nullable();
           $table->text('bed_types')->nullable();
           $table->text('added_amenities')->nullable();
           $table->string('dimension')->nullable();
           $table->text('image_paths')->nullable();
           $table->float('price')->nullable();
           $table->integer('similiar_rooms')->nullable();
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
