<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(0);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Table Fields
            $table->unsignedBigInteger('property_type_id');
            $table->string('name');
            $table->string('text_location')->nullable();
            $table->string('geolocation')->nullable();
            $table->string('street_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            $table->text('about_us')->nullable();
            $table->text('summary_text')->nullable();
            $table->text('primary_telephone')->nullable();
            $table->text('secondary_telephone')->nullable();
            $table->text('email')->nullable();
            $table->text('nearby_locations')->nullable();
            $table->text('serve_breakfast')->nullable();
            $table->text('languages_spoken')->nullable();
            $table->string('images_ids', 255)->nullable();
            $table->text('images_paths')->nullable();
            // Foreign Keys
            $table->foreign('property_type_id')->references('id')->on('property_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
