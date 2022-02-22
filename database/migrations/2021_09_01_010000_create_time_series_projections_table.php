<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('time_series_projections', function (Blueprint $table) {
            $table->id();

            $table->string('projection_name');
            $table->string('key')->nullable();
            $table->string('period');
            $table->timestamp('start_date')->nullable();
            $table->json('content');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_series_projections');
    }
};
