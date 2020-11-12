<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLoginhistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loginhistory', function (Blueprint $table) {
            $table->id();
            $table->integer('UserID');
            $table->string('Provider');
            $table->text('LoginToken');
            $table->text('IPAddress');
            $table->text('Latitude');
            $table->text('Longitude');
            $table->text('CountryShort');
            $table->text('CountryFull');
            $table->text('City');
            $table->text('Region');
            $table->text('Timezone');
            $table->text('BrowserType');
            $table->text('DeviceType');
            $table->dateTime('SignInDate')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('SignOutDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loginhistory');
    }
}
