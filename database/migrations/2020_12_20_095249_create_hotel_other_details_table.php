<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelOtherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_other_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->char('status', 2)->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();

            $table->unsignedBigInteger('property_id');
            $table->unsignedBigInteger('hotel_details_id')->nullable();
            $table->string('listed_on')->nullable();
            $table->string('star_rating')->nullable();
            $table->string('own_multiple_hotel')->nullable();
            $table->string('name_of_company_group_chain')->nullable();
            $table->string('use_channel_manager')->nullable();
            $table->string('channel_manager_name')->nullable();
            $table->text('parking_options')->nullable();
            $table->text('extra_bed_options')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_other_details');
    }
}
