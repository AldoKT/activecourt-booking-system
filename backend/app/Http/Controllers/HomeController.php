<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $sports = Field::selectRaw('type_fields, count(*) as count, MIN(image) as image, MIN(description) as description')
            ->groupBy('type_fields')
            ->get();
        return view('layouts.welcome', compact('sports'));
    }
}