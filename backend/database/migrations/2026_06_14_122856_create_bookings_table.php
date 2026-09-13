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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('id_bookings');
            $table->foreignId('user_id')->constrained('users', 'id_users')->onDelete('cascade');
            $table->foreignId('schedule_id')->constrained('schedules', 'id_schedules')->onDelete('cascade');
            $table->string('booking_code')->unique(); // [cite: 30]
            $table->integer('total_price'); // [cite: 31]
            $table->enum('status_bookings', ['Pending', 'Paid', 'Cancelled']); // [cite: 32]
            $table->date('play_date'); // [cite: 34]
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->timestamps(); // timestamp_bookings [cite: 35]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
