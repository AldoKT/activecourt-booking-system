<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $primaryKey = 'id_facilities';

    // Kolom yang diizinkan untuk pengisian massal
    protected $fillable = [
        'field_id',
        'name_facilities',
        'icon',
    ];

    public function field()
    {
        return $this->belongsTo(Field::class, 'field_id', 'id_fields');
    }
}