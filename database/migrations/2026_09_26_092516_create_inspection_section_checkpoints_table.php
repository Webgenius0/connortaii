<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inspection_section_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_section_id')->constrained()->cascadeOnDelete();

            // e.g. roof_type_material, overall_condition, roof_covering, gutters_downpipes, flashings_penetrations
            $table->string('checkpoint_key');
            $table->string('label'); // "Roof Type / Material", "Overall Condition"...

            $table->string('type')->nullable(); // ছবিতে "Type" ফিল্ড
            $table->enum('level_of_concern', ['green', 'orange', 'red', 'na'])->nullable();
            $table->text('observations')->nullable();

            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['inspection_section_id', 'checkpoint_key'], 'section_checkpoint_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspection_section_checkpoints');
    }
};
