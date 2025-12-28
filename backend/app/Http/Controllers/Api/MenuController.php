<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Display a listing of active menu items.
     * Cached for 1 hour (3600 seconds) for optimal performance.
     * Cache is automatically cleared when menus are created/updated/deleted.
     */
    public function index(): JsonResponse
    {
        $menus = Cache::remember('api.menus', 3600, function () {
            return Menu::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->select([
                    'id',
                    'label',
                    'url',
                    'route',
                    'target',
                    'icon',
                    'css_class',
                    'sort_order',
                ])
                ->get()
                ->map(fn ($menu) => [
                    'id' => $menu->id,
                    'label' => $menu->label,
                    'url' => $menu->url,
                    'route' => $menu->route,
                    'target' => $menu->target ?? '_self',
                    'icon' => $menu->icon,
                    'css_class' => $menu->css_class,
                    'sort_order' => $menu->sort_order,
                ])
                ->values()
                ->all();
        });

        return response()->json($menus, 200, [], JSON_UNESCAPED_SLASHES);
    }
}

