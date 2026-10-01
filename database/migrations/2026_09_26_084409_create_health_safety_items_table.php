<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('health_safety_items', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // identifiable, phone_tablet, tools, ppe, occupants, height, electricity, slips_trips_falls, chemicals
            $table->string('label');
            $table->boolean('is_hazard')->default(false);
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_safety_items');
    }
};
