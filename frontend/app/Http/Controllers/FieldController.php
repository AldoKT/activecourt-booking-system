<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;

class FieldController extends Controller
{
    public function index(Request $request)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        
        // Fetch from backend API
        $response = Http::get($backendUrl . '/fields', $request->all());

        $fieldsData = $response->json() ?? [];
        
        // Manual pagination on the frontend
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $itemCollection = collect($fieldsData);
        $perPage = 6;
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values()->all();

        // Convert recursively to real hydrated models to keep Blade compatibility
        $currentPageItems = array_map(function($item) {
            return $this->hydrateField((array) $item);
        }, $currentPageItems);

        $fields = new LengthAwarePaginator(
            $currentPageItems,
            count($itemCollection),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('fields.index', compact('fields'));
    }

    public function show($id)
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        
        $response = Http::get($backendUrl . '/fields/' . $id);
        
        if (!$response->successful()) {
            abort(404);
        }

        $field = $this->hydrateField($response->json());

        // Prepare JSON strings in Controller to keep Blade files clean and logic-free
        $schedulesJson = $field->schedules->sortBy('start_time')->values()->toJson();
        $subcourtsJson = json_encode($field->sub_courts);

        return view('fields.show', compact('field', 'schedulesJson', 'subcourtsJson'));
    }

    /**
     * Helper to hydrate raw backend array data into a Field Eloquent model
     */
    private function hydrateField($item)
    {
        if (!$item) return null;

        $field = new \App\Models\Field();
        $field->exists = true;
        
        $attributes = collect($item)->except(['facilities', 'schedules'])->toArray();
        $field->forceFill($attributes);

        if (isset($item['facilities'])) {
            $facilities = collect($item['facilities'])->map(function($fac) {
                $facility = new \App\Models\Facility();
                $facility->exists = true;
                $facility->forceFill((array) $fac);
                return $facility;
            });
            $field->setRelation('facilities', $facilities);
        }

        if (isset($item['schedules'])) {
            $schedules = collect($item['schedules'])->map(function($sch) {
                $schedule = new \App\Models\Schedule();
                $schedule->exists = true;
                $schedule->forceFill((array) $sch);
                return $schedule;
            });
            $field->setRelation('schedules', $schedules);
        }

        return $field;
    }
}