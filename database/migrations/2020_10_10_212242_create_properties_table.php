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
            // fields
            $table->string('name');
            $table->string('about_us')->nullable();
            $table->string('primary_telephone')->nullable();
            $table->string('secondary_telephone')->nullable();
            $table->string('longitude_latitude')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('country_region')->nullable();
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
