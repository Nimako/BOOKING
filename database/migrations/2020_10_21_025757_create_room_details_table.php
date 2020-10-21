<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->char('status', 2)->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Table Fields
            $table->unsignedBigInteger('room_id');
            $table->string('room_name',255);
            $table->string('bed_type',255)->nullable();
            $table->integer('bed_type_qty')->nullable();
            $table->string('added_amenity_ids', 255)->nullable();
            $table->text('added_amenity_text')->nullable();
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
        Schema::dropIfExists('room_details');
    }
}
