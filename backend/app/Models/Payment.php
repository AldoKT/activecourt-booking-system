<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';
    protected $primaryKey = 'id_payments';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'booking_id',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'amount',
        'payment_method',
        'status_payments',
        'snap_token',
        'paid_at',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id_bookings');
    }
}