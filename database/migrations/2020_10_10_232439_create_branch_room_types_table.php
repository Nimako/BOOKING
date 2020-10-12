<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchRoomTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_room_types', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Column
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Field
            $table->unsignedBigInteger('branch_id');
            $table->string('roomtype_ids', 255)->nullable();
            $table->text('roomtype_values')->nullable()->comment("Format => ...");
            $table->string('best_fit')->nullable();
            $table->string('image_ids')->nullable();
            $table->text('image_urls')->nullable();
            $table->string('room_size')->nullable();
            // Foreign Keys
            $table->foreign('branch_id')->references('id')->on('branches');
            //$table->foreign('room_type_id')->references('id')->on('room_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_room_types');
    }
}
