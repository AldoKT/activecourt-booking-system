<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data user
        $users = User::query()->where('role', 'user')->get();
        
        // 2. Ambil 3 data jadwal yang masih KOSONG (status '0')
        $schedules = Schedule::query()->where('status_schedules', 'Available')->take(3)->get();

        // Cek pengaman jika data kurang
        if ($users->isEmpty() ) {
            $this->command->info('BookingSeeder dilewati karena User tidak ada');
            return;
        }

        if ($schedules->count() < 3) {
            $this->command->info('BookingSeeder dilewati karena Jadwal kosong kurang dari 3.');
            return;
        }
        
        // Siapkan variabel
        $sched1 = $schedules[0];
        $sched2 = $schedules[1];
        $sched3 = $schedules[2];

        $user1 = $users[0] ?? $users->first();
        $user2 = $users[1] ?? $users->first();
        $user3 = $users[2] ?? $users->first();

        // 3. Buat rancangan data booking
        $bookingData = [
            [
                'booking_code' => '#AC-' . strtoupper(Str::random(6)),
                'play_date' => $sched1->date,
                'total_price' => 80000,
                'status_bookings' => 'Paid',
                'user_id' => $user1->id_users ?? $user1->id,
                'schedule_id' => $sched1->id_schedules ?? $sched1->id,
            ],
            [
                'booking_code' => '#AC-' . strtoupper(Str::random(6)),
                'play_date' => $sched2->date,
                'total_price' => 65000,
                'status_bookings' => 'Pending',
                'user_id' => $user2->id_users ?? $user2->id,
                'schedule_id' => $sched2->id_schedules ?? $sched2->id,
            ],
            [
                'booking_code' => '#AC-' . strtoupper(Str::random(6)),
                'play_date' => $sched3->date,
                'total_price' => 120000,
                'status_bookings' => 'Cancelled',
                'user_id' => $user3->id_users ?? $user3->id,
                'schedule_id' => $sched3->id_schedules ?? $sched3->id,
            ],
        ];

        // 4. Proses pembuatan Booking dan Update Status Schedule
        foreach ($bookingData as $data) {
            Booking::create($data);

            // Jika status booking bukan dibatalkan, maka ubah status lapangan menjadi '1' (Dibooking)
            // Jika dibatalkan, biarkan tetap '0' (Tersedia)
            if ($data['status_bookings'] !== 'Cancelled') {
                Schedule::query()
                    ->where('id_schedules', $data['schedule_id'])
                    ->update(['status_schedules' => '1']);
            }
        }
    }
}