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

            $table->string('name');
            $table->string('key')->nullable();
            $table->string('period');
            $table->timestamp('start_date');
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
