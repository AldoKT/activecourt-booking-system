<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $bookings = Booking::all();

        if ($bookings->isEmpty()) return;

        foreach ($bookings as $booking) {
            // Kita tidak membuat payment jika booking dibatalkan (Cancelled)
            if ($booking->status_bookings !== 'Cancelled') {
                Payment::create([
                    'booking_id' => $booking->id_bookings ?? $booking->id,
                    'midtrans_order_id' => 'ORDER-' . time() . '-' . ($booking->id_bookings ?? $booking->id),
                    'midtrans_transaction_id' => Str::uuid()->toString(),
                    'amount' => $booking->total_price,
                    'payment_method' => 'bank_transfer',
                    'status_payments' => $booking->status_bookings === 'Paid' ? 'settlement' : 'pending',
                    'paid_at' => $booking->status_bookings === 'Paid' ? now() : null,
                ]);
            }
        }
    }
}