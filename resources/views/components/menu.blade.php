@props(['menu' => null])

@if(is_null($menu))
    @php
        $menu = \YourVendor\FilamentMenuBuilder\Models\Menu::getTree();
    @endphp
@endif

<ul {{ $attributes->merge(['class' => 'menu']) }}>
    @foreach($menu as $menuItem)
        @if($menuItem->is_active)
            <li class="menu-item">
                <a href="{{ $menuItem->url }}" 
                   target="{{ $menuItem->target }}" 
                   class="menu-link {{ request()->url() === url($menuItem->url) ? 'active' : '' }}">
                    @if($menuItem->icon)
                        <span class="menu-icon">
                            @svg($menuItem->icon, 'w-5 h-5')
                        </span>
                    @endif
                    <span class="menu-text">{{ $menuItem->name }}</span>
                </a>
                
                @if($menuItem->children && $menuItem->children->count() > 0)
                    <x-filament-menu-builder::menu :menu="$menuItem->children" class="submenu" />
                @endif
            </li>
        @endif
    @endforeach
</ul>

<style>
.menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-item {
    position: relative;
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.5rem 1rem;
    text-decoration: none;
    color: #374151;
    transition: all 0.2s ease;
    border-radius: 0.375rem;
}

.menu-link:hover {
    background-color: #f3f4f6;
}

.menu-link.active {
    background-color: #e5e7eb;
    font-weight: 500;
}

.menu-icon {
    margin-right: 0.5rem;
    display: flex;
    align-items: center;
}

.submenu {
    padding-left: 1rem;
    border-left: 1px solid #e5e7eb;
    margin-left: 1rem;
}

@media (max-width: 768px) {
    .menu-link {
        padding: 0.75rem 1rem;
    }
}
</style>