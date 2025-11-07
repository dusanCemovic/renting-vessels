<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('vessel_id')->nullable()->constrained('vessels'); // assigned vessel
            $table->timestamp('start_at');
            $table->timestamp('end_at');
            $table->json('required_equipment')->nullable();
            $table->enum('status', ['scheduled','completed','cancelled','maintenance'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tasks');
    }
};
