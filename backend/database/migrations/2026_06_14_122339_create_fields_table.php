<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->id('id_fields'); // Primary Key 
            $table->string('name_fields');
            $table->string('type_fields'); // Futsal, Badminton, Basketball, dll.
            
            // Kolom baru untuk alamat spesifik
            $table->string('address'); 
            
            $table->text('description'); // Kini murni hanya untuk penjelasan fasilitas/lapangan
            $table->integer('price_per_hour');
            $table->integer('capacity');
            $table->string('image')->nullable(); // Ditambah nullable agar tidak error saat seeder kosong
            $table->json('sub_courts'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};