<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('inspection_health_safety_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('health_safety_item_id')->constrained()->cascadeOnDelete();

            $table->boolean('is_checked')->default(false);
            $table->boolean('has_concern')->default(false);
            $table->text('concern_details')->nullable();

            $table->timestamps();

            $table->unique(['inspection_id', 'health_safety_item_id'], 'inspection_item_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_health_safety_responses');
    }
};
