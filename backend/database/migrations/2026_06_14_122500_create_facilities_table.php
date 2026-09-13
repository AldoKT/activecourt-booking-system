<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id('id_facilities'); // Primary Key 

            // Foreign key dipisah langsung merujuk ke id_fields
            $table->foreignId('field_id')
                ->constrained('fields', 'id_fields')
                ->onDelete('cascade');

            $table->string('name_facilities');
            $table->string('icon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
