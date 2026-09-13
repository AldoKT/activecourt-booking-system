<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'id_bookings';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'user_id',
        'schedule_id',
        'schedule_ids',
        'subcourt_name',
        'booking_code',
        'total_price',
        'status_bookings',
        'play_date',
        'cancelled_at',
        'cancel_reason',
    ];

    protected $casts = [
        'schedule_ids' => 'array',
    ];

    protected $appends = [
        'computed_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_users');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id_schedules');
    }

    public function getSchedulesList()
    {
        if ($this->schedule_ids && is_array($this->schedule_ids)) {
            return Schedule::whereIn('id_schedules', $this->schedule_ids)
                ->orderBy('start_time')
                ->get();
        }
        return collect([$this->schedule])->filter();
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'id_bookings');
    }

    public function getComputedStatusAttribute()
    {
        if ($this->status_bookings === 'Pending') {
            return 'Waiting for Payment';
        } elseif ($this->status_bookings === 'Cancelled') {
            if ($this->cancel_reason === 'Rescheduled') {
                return 'Rescheduled';
            }
            return 'Canceled';
        } elseif ($this->status_bookings === 'Paid') {
            $playDate = \Carbon\Carbon::parse($this->play_date);
            $schedules = $this->getSchedulesList();
            
            if ($schedules->isNotEmpty()) {
                $lastSchedule = $schedules->last();
                $endDateTime = \Carbon\Carbon::parse($this->play_date . ' ' . $lastSchedule->end_time);
                if (now()->greaterThan($endDateTime)) {
                    return 'Done';
                }
            } else {
                if (now()->startOfDay()->greaterThan($playDate)) {
                    return 'Done';
                }
            }
            return 'Paid';
        }
        
        return $this->status_bookings;
    }
}
