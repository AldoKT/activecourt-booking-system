<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoCancelPending extends Command
{
    protected $signature = 'bookings:auto-cancel';
    protected $description = 'Auto-cancel pending bookings that exceed 30 minutes';

    public function handle()
    {
        $cutoff = Carbon::now()->subMinutes(30);

        $expiredBookings = Booking::query()
            ->with('schedule.field')
            ->where('status_bookings', 'Pending')
            ->where('created_at', '<', $cutoff)
            ->get();

        $count = 0;

        foreach ($expiredBookings as $booking) {
            // Use schedule_ids JSON if available, fall back to single schedule_id
            $scheduleIdsToFree = $booking->schedule_ids;

            if (!empty($scheduleIdsToFree) && is_array($scheduleIdsToFree)) {
                Schedule::query()
                    ->whereIn('id_schedules', $scheduleIdsToFree)
                    ->update(['status_schedules' => 'Available']);
            } else {
                // Fallback for old records
                $pricePerHour = $booking->schedule?->field?->price_per_hour ?? 0;
                $duration = ($pricePerHour <= 0) ? 1 : round($booking->total_price / $pricePerHour);

                $schedulesToFree = Schedule::query()
                    ->where('field_id', $booking->schedule->field_id)
                    ->where('date', $booking->play_date)
                    ->where('start_time', '>=', $booking->schedule->start_time)
                    ->orderBy('start_time')
                    ->limit($duration)
                    ->pluck('id_schedules');

                if ($schedulesToFree->isNotEmpty()) {
                    Schedule::query()
                        ->whereIn('id_schedules', $schedulesToFree)
                        ->update(['status_schedules' => 'Available']);
                }
            }

            $booking->update([
                'status_bookings' => 'Cancelled',
                'cancelled_at' => now(),
                'cancel_reason' => 'Otomatis dibatalkan — melebihi batas waktu pembayaran 30 menit',
            ]);

            $count++;
        }

        $this->info("Auto-cancelled {$count} expired pending bookings.");
    }
}
