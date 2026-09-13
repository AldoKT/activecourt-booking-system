<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'id_schedules';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'field_id',
        'date',
        'start_time',
        'end_time',
        'status_schedules',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'id_fields');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'schedule_id', 'id_schedules');
    }
}
