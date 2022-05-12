<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenreRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'username',
        'user_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',

    ];
}
