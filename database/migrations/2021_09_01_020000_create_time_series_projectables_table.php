<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('time_series_projectables', function (Blueprint $table) {
            $table->foreignId('projection_id')
                ->constrained('time_series_projections')
                ->onDelete('cascade');

            $table->unsignedBigInteger('projectable_id');
            $table->string('projectable_type');

            // add composite key?
        });
    }

    public function down()
    {
        Schema::dropIfExists('time_series_projectables');
    }
};
