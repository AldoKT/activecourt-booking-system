<?php

namespace Database\Seeders;

use App\Models\Field;
use Illuminate\Database\Seeder;

class FieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            [
                'name_fields' => 'Elite Indoor Arena',
                'type_fields' => 'Futsal',
                'address' => 'Jl. Sudirman No. 18, Surabaya', // Dipisah ke sini
                'description' => 'Sangat cocok untuk pertandingan persahabatan maupun turnamen resmi dengan sirkulasi udara yang baik.',
                'price_per_hour' => 80000,
                'capacity' => 15,
                'image' => null, 
                'sub_courts' => [
                    ['name' => 'Lapangan A', 'type' => 'Lantai Karpet Pro'],
                    ['name' => 'Lapangan B', 'type' => 'Lantai Karpet Pro'],
                ],
            ],
            [
                'name_fields' => 'Badminton Arena',
                'type_fields' => 'Badminton',
                'address' => 'Jl. Sinisuka No. 100, Surabaya', // Dipisah ke sini
                'description' => 'Menggunakan standar lantai BWF untuk kenyamanan bermain dan meminimalisir cedera lutut.',
                'price_per_hour' => 60000,
                'capacity' => 4,
                'image' => null,
                'sub_courts' => [
                    ['name' => 'Court 1', 'type' => 'Lantai Karpet BWF'],
                    ['name' => 'Court 2', 'type' => 'Lantai Karpet BWF'],
                    ['name' => 'Court 3', 'type' => 'Lantai Karpet BWF'],
                ],
            ],
            [
                'name_fields' => 'Basketball Arena',
                'type_fields' => 'Basketball',
                'address' => 'Jl. Bisabisa No. 90, Surabaya', // Dipisah ke sini
                'description' => 'Lapangan basket indoor premium dengan papan skor digital, ruang ganti AC, dan tribun penonton.',
                'price_per_hour' => 90000,
                'capacity' => 20,
                'image' => null,
                'sub_courts' => [
                    ['name' => 'Main Court', 'type' => 'Lantai Kayu Jati Premium'],
                ],
            ],
        ];

        foreach ($fields as $field) {
            Field::query()->create($field);
        }
    }
}