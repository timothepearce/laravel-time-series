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

            // Add necessary fields...

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cargo_projections');
    }
};
