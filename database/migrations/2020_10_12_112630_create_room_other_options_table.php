<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomOtherOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_other_options', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Column
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Field
            $table->unsignedBigInteger('room_id');
            $table->text('added_amenities')->nullable();
            $table->string('added_amenity_ids')->nullable();
            $table->float('price')->nullable();
            // Foreign Keys
            $table->foreign('room_id')->references('id')->on('branch_room_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_other_options');
    }
}
