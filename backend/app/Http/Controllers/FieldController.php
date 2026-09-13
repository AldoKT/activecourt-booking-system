<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $query = Field::with('facilities');

        if ($location = $request->location) {
            $query->where(function ($q) use ($location) {
                $q->where('address', 'like', '%' . $location . '%')
                  ->orWhere('description', 'like', '%' . $location . '%');
            });
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_fields', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($sports = $request->sports) {
            $query->whereIn('type_fields', (array) $sports);
        }

        if ($maxPrice = $request->max_price) {
            $query->where('price_per_hour', '<=', (int) $maxPrice);
        }

        if ($sort = $request->sort) {
            if ($sort === 'lowest_price' || $sort === 'Harga Terendah') {
                $query->orderBy('price_per_hour', 'asc');
            } elseif ($sort === 'highest_price' || $sort === 'Harga Tertinggi') {
                $query->orderBy('price_per_hour', 'desc');
            } elseif ($sort === 'popular' || $sort === 'Terpopuler') {
                $query->withCount('schedules')->orderBy('schedules_count', 'desc');
            } elseif ($sort === 'newest' || $sort === 'Terbaru') {
                $query->latest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $fields = $query->paginate(6)->withQueryString();

        return view('fields.index', compact('fields'));
    }

    public function show(Field $field)
    {
        // TAMBAHKAN 'schedules' di sini
        $field->load(['facilities', 'schedules']);
        
        return view('fields.show', compact('field'));
    }

    public function apiIndex(Request $request)
    {
        $query = Field::with('facilities');

        if ($location = $request->location) {
            $query->where(function ($q) use ($location) {
                $q->where('address', 'like', '%' . $location . '%')
                  ->orWhere('description', 'like', '%' . $location . '%');
            });
        }

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_fields', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($sports = $request->sports) {
            $query->whereIn('type_fields', (array) $sports);
        }

        if ($maxPrice = $request->max_price) {
            $query->where('price_per_hour', '<=', (int) $maxPrice);
        }

        if ($sort = $request->sort) {
            if ($sort === 'lowest_price' || $sort === 'Harga Terendah') {
                $query->orderBy('price_per_hour', 'asc');
            } elseif ($sort === 'highest_price' || $sort === 'Harga Tertinggi') {
                $query->orderBy('price_per_hour', 'desc');
            } elseif ($sort === 'popular' || $sort === 'Terpopuler') {
                $query->withCount('schedules')->orderBy('schedules_count', 'desc');
            } elseif ($sort === 'newest' || $sort === 'Terbaru') {
                $query->latest();
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $fields = $query->get();
        return response()->json($fields);
    }

    public function apiShow($id)
    {
        $field = Field::with(['facilities', 'schedules' => function ($query) {
            $query->orderBy('start_time', 'asc');
        }])->find($id);
        
        if (!$field) {
            return response()->json(['message' => 'Field not found'], 404);
        }

        // Get all active bookings for this field
        $activeBookings = \App\Models\Booking::whereIn('status_bookings', ['Pending', 'Paid', 'Done'])
            ->where('play_date', '>=', now()->toDateString())
            ->get();

        foreach ($field->schedules as $schedule) {
            $scheduleBookings = $activeBookings->filter(function($booking) use ($schedule) {
                $ids = $booking->schedule_ids ?? [];
                if (is_string($ids)) {
                    $ids = json_decode($ids, true);
                }
                return in_array((string)$schedule->id_schedules, array_map('strval', $ids));
            })->values();
            
            $schedule->bookings = $scheduleBookings;
        }

        return response()->json($field);
    }
}
