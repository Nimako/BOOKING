<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommonPropertyPoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('common_property_policies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Fields
            $table->unsignedBigInteger('property_id');
            $table->string('sub_policy_ids', 255)->nullable();
            $table->text('sub_policy_text')->nullable();
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
        Schema::dropIfExists('common_property_policies');
    }
}
