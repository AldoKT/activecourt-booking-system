<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();

        if ($fields->isEmpty()) return;

        $schedules = [];
        $now = now();

        // Membuat jadwal dari 3 hari yang lalu hingga 7 hari ke depan (Total 11 hari)
        for ($i = -3; $i <= 7; $i++) {
            $date = $now->copy()->addDays($i)->toDateString();

            foreach ($fields as $field) {
                // Looping dari jam 07:00 sampai 20:00 (Slot terakhir adalah 20:00 - 21:00)
                for ($hour = 7; $hour <= 20; $hour++) {
                    $startTime = sprintf('%02d:00:00', $hour);
                    $endTime = sprintf('%02d:00:00', $hour + 1);
                    $status = 'Available'; // Default: '0' (Tersedia)

                    $schedules[] = [
                        'field_id' => $field->id_fields ?? $field->id,
                        'date' => $date,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status_schedules' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Menggunakan insert() dan array_chunk agar database tidak terbebani 
        // saat memasukkan ratusan baris data sekaligus
        foreach (array_chunk($schedules, 100) as $chunk) {
            Schedule::insert($chunk);
        }
    }
}