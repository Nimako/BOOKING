<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_ratings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Fields
            $table->float('value');
            $table->unsignedBigInteger('property_id');
            $table->string('criteria_ids', 255)->nullable();
            $table->text('criteria_values')->nullable();
            // Foreign Keys
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
        Schema::dropIfExists('property_ratings');
    }
}
