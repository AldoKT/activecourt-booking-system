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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id('id_schedules'); // [cite: 24]
            $table->foreignId('field_id')->constrained('fields', 'id_fields')->onDelete('cascade');
            $table->date('date'); // [cite: 25]
            $table->time('start_time'); // [cite: 26]
            $table->time('end_time'); // [cite: 27]
            $table->enum('status_schedules', ['Available', 'Booked', 'Locked']); // [cite: 28]
            $table->timestamps(); // timestamp_schedules [cite: 29]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
