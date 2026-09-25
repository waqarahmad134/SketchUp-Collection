<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_path',
        'to_path',
        'status_code',
        'hits',
        'is_active',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'hits' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Normalize a request path for lookup: leading slash, no trailing slash,
     * no query string.
     */
    public static function normalizePath(string $path): string
    {
        $path = parse_url($path, PHP_URL_PATH) ?: '/';
        $path = '/' . ltrim($path, '/');
        if (strlen($path) > 1) {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
