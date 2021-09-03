<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cargo_projections', function (Blueprint $table) {
            $table->id();

            $table->string('model_name');
            $table->string('interval_name');

            $table->timestamp('interval_start');
            $table->timestamp('interval_end');

            $table->json('content');

            $table->timestamps();

            // Add composite key?
        });
    }

    public function down()
    {
        Schema::dropIfExists('cargo_projections');
    }
};
