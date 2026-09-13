<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $fields = Field::with('schedules')->get();
        
        $query = Schedule::with('field', 'booking.user');

        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $schedules = $query->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('admin.schedules', compact('fields', 'schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id_fields',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $intervalMinutes = (int) $request->interval;
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $fieldId = $request->field_id;

        $created = 0;

        for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
            $dateString = $d->format('Y-m-d');
            $current = $start->copy();

            while ($current->copy()->addMinutes($intervalMinutes) <= $end) {
                $slotStart = $current->format('H:i:s');
                $slotEnd = $current->copy()->addMinutes($intervalMinutes)->format('H:i:s');

                $exists = Schedule::where('field_id', $fieldId)
                    ->where('date', $dateString)
                    ->where('start_time', $slotStart)
                    ->exists();

                if (!$exists) {
                    Schedule::create([
                        'field_id' => $fieldId,
                        'date' => $dateString,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'status_schedules' => 'Available',
                    ]);
                    $created++;
                }

                $current->addMinutes($intervalMinutes);
            }
        }

        if ($created > 0) {
            return redirect()->route('admin.schedules')->with('success', "$created schedules added successfully.");
        }

        return redirect()->route('admin.schedules')->with('info', 'No new schedules added (they might already exist).');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date',
            'status_schedules' => 'required|in:Available,Booked,Locked',
        ]);

        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'status_schedules' => $request->status_schedules,
        ]);

        return redirect()->route('admin.schedules')->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings === 'Paid') {
            return back()->with('error', 'Cannot delete a schedule that is already booked and paid.');
        }

        $schedule->delete();

        return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings !== 'Cancelled') {
            return back()->with('error', 'Cannot change status of a currently booked schedule.');
        }

        $newStatus = $schedule->status_schedules === 'Available' ? 'Locked' : 'Available';
        $schedule->update(['status_schedules' => $newStatus]);

        return redirect()->route('admin.schedules')->with('success', "Schedule status changed to $newStatus.");
    }

    public function destroyAll(Request $request)
    {
        $query = Schedule::query();
        
        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $deleted = $query->where('status_schedules', '!=', 'Booked')->delete();

        return back()->with('success', $deleted . ' schedules deleted successfully (booked schedules were not affected).');
    }

    public function apiIndex(Request $request)
    {
        $fields = Field::with('schedules')->get();
        $query = Schedule::with('field', 'booking.user');

        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $schedules = $query->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return response()->json([
            'fields' => $fields,
            'schedules' => $schedules
        ]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id_fields',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'interval' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $intervalMinutes = (int) $request->interval;
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $fieldId = $request->field_id;

        $created = 0;

        for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
            $dateString = $d->format('Y-m-d');
            $current = $start->copy();

            while ($current->copy()->addMinutes($intervalMinutes) <= $end) {
                $slotStart = $current->format('H:i:s');
                $slotEnd = $current->copy()->addMinutes($intervalMinutes)->format('H:i:s');

                $exists = Schedule::where('field_id', $fieldId)
                    ->where('date', $dateString)
                    ->where('start_time', $slotStart)
                    ->exists();

                if (!$exists) {
                    Schedule::create([
                        'field_id' => $fieldId,
                        'date' => $dateString,
                        'start_time' => $slotStart,
                        'end_time' => $slotEnd,
                        'status_schedules' => 'Available',
                    ]);
                    $created++;
                }

                $current->addMinutes($intervalMinutes);
            }
        }

        return response()->json([
            'success' => true,
            'created' => $created,
            'message' => $created > 0 ? "$created schedules added successfully." : "No new schedules added."
        ]);
    }

    public function apiUpdate(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'date' => 'required|date',
            'status_schedules' => 'required|in:Available,Booked,Locked',
        ]);

        $schedule->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
            'status_schedules' => $request->status_schedules,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'schedule' => $schedule
        ]);
    }

    public function apiDestroy($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings === 'Paid') {
            return response()->json(['success' => false, 'message' => 'Cannot delete a schedule that is already booked and paid.'], 422);
        }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.'
        ]);
    }

    public function apiToggleStatus($id)
    {
        $schedule = Schedule::with('booking')->findOrFail($id);

        if ($schedule->booking && $schedule->booking->status_bookings !== 'Cancelled') {
            return response()->json(['success' => false, 'message' => 'Cannot change status of a currently booked schedule.'], 422);
        }

        $newStatus = $schedule->status_schedules === 'Available' ? 'Locked' : 'Available';
        $schedule->update(['status_schedules' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => "Schedule status changed to $newStatus.",
            'newStatus' => $newStatus
        ]);
    }

    public function apiDestroyAll(Request $request)
    {
        $query = Schedule::query();
        
        if ($request->has('filter_start_date') && $request->filter_start_date && $request->has('filter_end_date') && $request->filter_end_date) {
            $query->whereBetween('date', [$request->filter_start_date, $request->filter_end_date]);
        } elseif ($request->has('filter_date') && $request->filter_date) {
            $query->whereDate('date', $request->filter_date);
        } else {
            $query->whereDate('date', '>=', Carbon::today());
        }

        if ($request->has('filter_field_id') && $request->filter_field_id) {
            $query->where('field_id', $request->filter_field_id);
        }

        $deleted = $query->where('status_schedules', '!=', 'Booked')->delete();

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
            'message' => "$deleted schedules deleted successfully."
        ]);
    }
}
