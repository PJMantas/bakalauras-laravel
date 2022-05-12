<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'video_create',
        'video_edit',
        'video_delete',

        'reaction_create',
        'comment_create',
        'comment_edit',
        'comment_delete',

        // admin-only permissions
        'is_admin',
        'manage_users',
        'manage_permissions',
        'manage_genres',
        
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];
}
