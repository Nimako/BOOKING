<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonRoomAmenitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_room_amenities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Column
            $table->char('status', 2)->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Field
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('popular_amenity_ids')->nullable();
            $table->text('popular_amenity_text')->nullable();
            $table->float('price')->nullable();
            // Foreign Keys
            //$table->foreign('room_id')->references('id')->on('room_apartments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('common_room_amenities');
    }
}
