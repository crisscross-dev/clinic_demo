<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsentSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'department',
        'start_time',
        'end_time',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean'
    ];
}
