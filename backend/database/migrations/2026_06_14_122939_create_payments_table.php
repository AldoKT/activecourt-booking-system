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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_payments'); // Primary Key

            // Foreign key merujuk ke id_bookings
            $table->foreignId('booking_id')
                ->constrained('bookings', 'id_bookings')
                ->onDelete('cascade');

            $table->string('midtrans_order_id')->unique();
            $table->string('midtrans_transaction_id')->nullable();
            $table->integer('amount');
            $table->string('payment_method')->nullable(); // Gopay, ShopeePay, Bank Transfer, dll.
            $table->string('status_payments'); // settlement, pending, deny, expire
            $table->string('snap_token')->nullable(); // Token untuk menampilkan pop-up Midtrans Snap
            $table->timestamp('paid_at')->nullable();
            $table->timestamps(); // timestamp_payments
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
