<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'original_title',
        'title_language',
        'summary',
        'cover',
        'backdrop',
        'preview_video',
        'logo',
        'tmdb_id',
        'release_date',
        'genre',
        'is_favorite',
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];
}
