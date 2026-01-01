<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CustomScript extends Model
{
    protected $fillable = [
        'name',
        'position',
        'code',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope to get active scripts
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get scripts by position
     */
    public function scopePosition(Builder $query, string $position): Builder
    {
        return $query->where('position', $position);
    }

    /**
     * Scope to get ordered scripts
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order');
    }

    /**
     * Get scripts for head section
     */
    public static function getHeadScripts(): \Illuminate\Support\Collection
    {
        return static::active()
            ->position('head')
            ->ordered()
            ->get();
    }

    /**
     * Get scripts for body start
     */
    public static function getBodyStartScripts(): \Illuminate\Support\Collection
    {
        return static::active()
            ->position('body_start')
            ->ordered()
            ->get();
    }

    /**
     * Get scripts for body end
     */
    public static function getBodyEndScripts(): \Illuminate\Support\Collection
    {
        return static::active()
            ->position('body_end')
            ->ordered()
            ->get();
    }
}
