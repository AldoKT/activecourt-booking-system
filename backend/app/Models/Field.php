<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'fields';
    protected $primaryKey = 'id_fields';

    protected $fillable = [
        'name_fields',
        'type_fields',
        'address',
        'description',
        'price_per_hour',
        'capacity',
        'image',
        'sub_courts',
    ];

    // Beri tahu Laravel bahwa kolom ini adalah JSON/Array
    protected $casts = [
        'sub_courts' => 'array',
    ];

    public function facilities()
    {
        return $this->hasMany(Facility::class, 'field_id', 'id_fields');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'field_id', 'id_fields');
    }
}