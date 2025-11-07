<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // e.g. 'jet', 'atr'
            $table->integer('size');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels');
    }
};
