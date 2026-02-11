<?php

namespace Vendor\FilamentMenuBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Menu extends Model
{
    protected $fillable = [
        'parent_id',
        'name',
        'url',
        'target',
        'icon',
        'order',
        'is_active',
        'is_external',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_external' => 'boolean',
        'parent_id' => 'integer',
        'order' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('parent_id', 'asc')->orderBy('order', 'asc');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function calculateDepth(): int
    {
        // Build ancestor chain via database to avoid relationship loading issues
        $depth = 0;
        $currentId = $this->parent_id;
        $visited = [];

        while ($currentId && !in_array($currentId, $visited)) {
            $visited[] = $currentId;
            $parent = self::find($currentId);
            if ($parent) {
                $depth++;
                $currentId = $parent->parent_id;
            } else {
                break;
            }
        }

        return $depth;
    }

    public function getIndentedName(): string
    {
        return str_repeat('— ', $this->calculateDepth()) . $this->name;
    }

    public static function getTree()
    {
        if (config('filament-menu-builder.cache_enabled')) {
            return Cache::remember(
                'menu_tree',
                config('filament-menu-builder.cache_duration'),
                function () {
                    return self::buildTree();
                }
            );
        }

        return self::buildTree();
    }

    protected static function buildTree()
    {
        $menus = self::ordered()->get();
        $tree = [];

        foreach ($menus as $menu) {
            $tree[] = $menu;
        }

        return self::buildTreeRecursive($tree);
    }

    protected static function buildTreeRecursive($menus, $parentId = null)
    {
        $branch = [];

        foreach ($menus as $menu) {
            if ($menu->parent_id == $parentId) {
                $children = self::buildTreeRecursive($menus, $menu->id);
                if ($children) {
                    $menu->children = $children;
                }
                $branch[] = $menu;
            }
        }

        return $branch;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($menu) {
            // Sanitize URL
            if ($menu->url) {
                $url = trim($menu->url);
                if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
                    $url = '/' . ltrim($url, '/');
                }
                $menu->url = strip_tags($url);
            }

            // Sanitize route_name
            if ($menu->route_name) {
                $route = strtolower(trim($menu->route_name));
                $route = preg_replace('/[^a-z0-9._-]/', '.', $route);
                $route = preg_replace('/\.+/', '.', $route);
                $route = trim($route, '.');
                $menu->route_name = $route;
            }

            // Sanitize name - remove HTML, control chars, normalize whitespace
            if ($menu->name) {
                $name = strip_tags($menu->name);
                $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);
                $name = preg_replace('/\s+/', ' ', $name);
                $menu->name = trim($name);
            }

            // Ensure order is integer >= 0
            $order = (int) ($menu->order ?? 0);
            if ($order < 0) {
                $order = 0;
            }
            
            // Get all sibling menus (same parent, excluding self)
            $siblingsQuery = self::query()
                ->where('id', '!=', $menu->id ?: 0);
            
            if ($menu->parent_id) {
                $siblingsQuery->where('parent_id', $menu->parent_id);
            } else {
                $siblingsQuery->whereNull('parent_id');
            }
            
            $siblingOrders = $siblingsQuery->pluck('order')->map(fn ($o) => (int) $o)->toArray();
            
            // If requested order conflicts with existing sibling, find next available
            if (in_array($order, $siblingOrders, true)) {
                $maxOrder = $siblingsQuery->max('order') ?? -1;
                $order = (int) $maxOrder + 1;
            }
            
            $menu->order = $order;
        });

        static::saved(function ($menu) {
            if (config('filament-menu-builder.cache_enabled')) {
                Cache::forget('menu_tree');
            }
        });

        static::deleted(function ($menu) {
            if (config('filament-menu-builder.cache_enabled')) {
                Cache::forget('menu_tree');
            }
        });
    }
}