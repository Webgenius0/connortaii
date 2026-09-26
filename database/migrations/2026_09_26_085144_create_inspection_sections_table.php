<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {

        Schema::create('inspection_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained()->cascadeOnDelete();

            $table->enum('section_type', [
                'exterior_roof',
                'roof_space',
                'external_wall_cladding',
                'foundation_subfloor',
                'plumbing_system',
                'electrical_system',
                'weather_tightness',
                'site_improvements',
                'pest_potential_hazards',
                'interior_elements',
            ]);

            $table->enum('accessibility_status', ['pending', 'accessible', 'restricted'])->default('pending');
            $table->enum('restriction_reason', ['limited_access','obstruction','safety_concern','concealed_element','height','confined_space', 'other'])->nullable();
            $table->text('restriction_description')->nullable();

            $table->enum('overall_status', ['pending', 'pass', 'restricted', 'fail'])->default('pending');

            $table->timestamps();

            $table->unique(['inspection_id', 'section_type'], 'inspection_section_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspection_sections');
    }
};
