<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('quasar_projections', function (Blueprint $table) {
            $table->id();

            $table->string('projection_name');
            $table->string('key')->nullable();
            $table->string('period');
            $table->timestamp('start_date');
            $table->json('content');

            $table->timestamps();

            // @todo Add composite key?
        });
    }

    public function down()
    {
        Schema::dropIfExists('quasar_projections');
    }
};
