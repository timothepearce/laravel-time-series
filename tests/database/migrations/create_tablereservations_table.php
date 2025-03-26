<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('table_reservations', function (Blueprint $table) {
            $table->id();
            $table->integer('table_id');
            $table->string('customer_name');
            $table->dateTime('reservation_date');
            $table->dateTime('reservation_made_date');
            $table->integer('number_people');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_reservations');
    }
};
