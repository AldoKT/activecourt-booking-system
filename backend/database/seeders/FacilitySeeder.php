<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\Facility;
use Illuminate\Database\Seeder;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $fields = Field::all();

        if ($fields->isEmpty()) {
            return;
        }

        $defaultFacilities = [
            ['name_facilities' => 'Free WiFi', 'icon' => 'wifi'],
            ['name_facilities' => 'Shower Room', 'icon' => 'shower'],
            ['name_facilities' => 'Secure Parking', 'icon' => 'parking'],
            ['name_facilities' => 'Full AC Area', 'icon' => 'snowflake'],
        ];

        foreach ($fields as $field) {
            foreach ($defaultFacilities as $facility) {
                $field->facilities()->create($facility);
            }
        }
    }
}