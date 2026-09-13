<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            FieldSeeder::class,
            FacilitySeeder::class,
            ScheduleSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
