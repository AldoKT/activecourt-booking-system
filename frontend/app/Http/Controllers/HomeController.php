<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    public function index()
    {
        $backendUrl = env('BACKEND_API_URL', 'http://127.0.0.1:8000/api');
        
        $response = Http::get($backendUrl . '/fields');
        $fields = collect($response->json() ?? []);
        
        $sports = $fields->groupBy('type_fields')->map(function($group, $type) {
            return (object) [
                'type_fields' => $type,
                'count' => $group->count(),
                'image' => $group->min('image'),
                'description' => $group->min('description')
            ];
        })->values();

        return view('layouts.welcome', compact('sports'));
    }
}