<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Fields
            $table->unsignedBigInteger('property_id');
            $table->string('name')->nullable();
            $table->text('about_us')->nullable();
            $table->text('summary_text')->nullable();
            $table->string('street_address')->nullable();
            $table->string('gps_long_lat')->nullable();
            $table->string('nearby_places')->nullable();
            $table->text('general_notice')->nullable();
            // Contact Info
            $table->string('primary_telephone')->nullable();
            $table->string('secondary_telephone')->nullable();
            $table->string('country_region')->nullable();
            $table->string('apartment_floor_num')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('other_info')->nullable();
            // Foreign keys
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
}
