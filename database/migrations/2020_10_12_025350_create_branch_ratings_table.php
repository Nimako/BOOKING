<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_ratings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            // Central Columns
            $table->tinyInteger('status')->default(1);
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            // Fields
            $table->float('value');
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('criteria_id');
            $table->unsignedBigInteger('user_id');
            // Foreign keys
            $table->foreign('branch_id')->references('id')->on('branches');
            $table->foreign('criteria_id')->references('id')->on('criterias');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branch_ratings');
    }
}
