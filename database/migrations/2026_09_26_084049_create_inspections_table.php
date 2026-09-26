<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspector_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('inspector_name');
            $table->string('email')->nullable();
            $table->string('mobile_number')->nullable();

            $table->string('property_address')->nullable();
            $table->string('property_name')->nullable();
            $table->date('inspection_date')->nullable();

            $table->string('property_owner_name')->nullable();
            $table->boolean('property_owner_presence')->nullable();
            $table->boolean('other_people_presence')->nullable();
            $table->boolean('house_occupied')->nullable();
            $table->boolean('property_furnished')->nullable();

            $table->string('building_type')->nullable();
            $table->string('status_of_utilities')->nullable();
            $table->string('weather_during_inspection')->nullable();

            $table->enum('status', ['draft', 'in_progress', 'completed', 'stopped'])->default('draft');
            $table->unsignedTinyInteger('current_step')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
