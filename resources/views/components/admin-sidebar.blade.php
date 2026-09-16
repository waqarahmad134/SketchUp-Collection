@php
$navItems = [
    // Main
    ['href' => route('admin.dashboard'), 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'group' => 'main'],
    
    // Content Management
    ['href' => route('admin.posts.index'), 'label' => 'Posts', 'icon' => 'file-text', 'group' => 'content'],
    ['href' => route('admin.categories.index'), 'label' => 'Post Categories', 'icon' => 'folder-open', 'group' => 'content'],
    ['href' => route('admin.tags.index'), 'label' => 'Tags', 'icon' => 'tags', 'group' => 'content'],
    ['href' => route('admin.media.index'), 'label' => 'Media', 'icon' => 'image', 'group' => 'content'],
    ['href' => route('admin.comments.index'), 'label' => 'Comments', 'icon' => 'message-circle', 'group' => 'content'],
    ['href' => route('admin.newsletter.index'), 'label' => 'Newsletter', 'icon' => 'mail', 'group' => 'content'],
    ['href' => route('admin.redirects.index'), 'label' => 'Redirects', 'icon' => 'corner-up-right', 'group' => 'content'],
    
    // Shop Management
    ['href' => route('admin.products.index'), 'label' => 'Products', 'icon' => 'package', 'group' => 'shop'],
    ['href' => route('admin.product-categories.index'), 'label' => 'Product Categories', 'icon' => 'tag', 'group' => 'shop'],
    ['href' => route('admin.orders.index'), 'label' => 'Orders', 'icon' => 'shopping-cart', 'group' => 'shop'],
    ['href' => route('admin.coupons.index'), 'label' => 'Coupons', 'icon' => 'ticket', 'group' => 'shop'],
    ['href' => route('admin.transactions.index'), 'label' => 'Transactions', 'icon' => 'credit-card', 'group' => 'shop'],
    ['href' => route('admin.payment-gateways.index'), 'label' => 'Payment Gateways', 'icon' => 'wallet', 'group' => 'shop'],
    
    // User Management
    ['href' => route('admin.users.index'), 'label' => 'Users', 'icon' => 'users', 'group' => 'users'],
    
    // Settings
    ['href' => route('admin.menus.index'), 'label' => 'Menus', 'icon' => 'menu', 'group' => 'settings'],
    ['href' => route('admin.settings.index'), 'label' => 'Settings', 'icon' => 'settings', 'group' => 'settings'],
    ['href' => route('admin.custom-scripts.index'), 'label' => 'Custom Scripts', 'icon' => 'code', 'group' => 'settings'],
];

$groupedItems = [
    'main' => [],
    'content' => [],
    'shop' => [],
    'users' => [],
    'settings' => [],
];

foreach ($navItems as $item) {
    $group = $item['group'] ?? 'main';
    if (!isset($groupedItems[$group])) {
        $groupedItems[$group] = [];
    }
    $groupedItems[$group][] = $item;
}
@endphp

<div x-data="{ mobileMenuOpen: false }">
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2"
            data-testid="button-mobile-menu"
        >
            <template x-if="!mobileMenuOpen">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </template>
            <template x-if="mobileMenuOpen">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </template>
        </button>
    </div>

    <!-- Sidebar -->
    <div
        :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-sidebar border-r border-sidebar-border transform transition-transform duration-300 ease-in-out lg:translate-x-0"
    >
        <div class="flex flex-col h-full">
            <!-- Logo/Header -->
            <div class="p-6 border-b border-sidebar-border">
                <h1 class="text-xl font-bold text-sidebar-foreground">
                    Admin Panel
                </h1>
                <p class="text-sm text-muted-foreground mt-1">
                    {{ auth()->user()->name ?? 'Admin' }}
                </p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-4 overflow-y-auto">
                @foreach(['main', 'content', 'shop', 'users', 'settings'] as $groupKey)
                    @if(isset($groupedItems[$groupKey]) && count($groupedItems[$groupKey]) > 0)
                        @if($groupKey !== 'main')
                            <div class="pt-4 border-t border-sidebar-border">
                                <h3 class="px-3 mb-2 text-xs font-semibold text-muted-foreground uppercase tracking-wider">
                                    @if($groupKey === 'content') Content
                                    @elseif($groupKey === 'shop') Shop
                                    @elseif($groupKey === 'users') Users
                                    @elseif($groupKey === 'settings') Settings
                                @endif
                                </h3>
                            </div>
                        @endif
                        <div class="space-y-1">
                            @foreach($groupedItems[$groupKey] as $item)
                            @php
                                $routeName = str_replace(['admin.', '.index'], ['admin.', ''], str_replace(url('/'), '', $item['href']));
                                $isActive = request()->routeIs($routeName . '*') || request()->url() === $item['href'];
                            @endphp
                            <a
                                href="{{ $item['href'] }}"
                                @click="mobileMenuOpen = false"
                                class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors hover-elevate active-elevate-2 {{ $isActive ? 'bg-sidebar-accent text-sidebar-accent-foreground border-l-4 border-primary' : 'text-sidebar-foreground' }}"
                                data-testid="link-{{ strtolower(str_replace(' ', '-', $item['label'])) }}"
                            >
                                @if($item['icon'] === 'layout-dashboard')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                @elseif($item['icon'] === 'file-text')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'folder-open' || $item['icon'] === 'tag')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h12a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'tags')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'image')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'package')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($item['icon'] === 'shopping-cart')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'ticket')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'credit-card')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'users')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'menu')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                    </svg>
                                @elseif($item['icon'] === 'settings')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @elseif($item['icon'] === 'code')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                    </svg>
                                @elseif($item['icon'] === 'wallet')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12V7H5a2 2 0 010-4h14v4"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5v14a2 2 0 002 2h16V7"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12a1 1 0 100 2 1 1 0 000-2z"></path>
                                    </svg>
                                @endif
                                <span>{{ $item['label'] }}</span>
                            </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </nav>

            <!-- Logout Button -->
            <div class="p-4 border-t border-sidebar-border">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-start gap-3 whitespace-nowrap rounded-md text-sm font-medium min-h-9 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                        data-testid="button-logout"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overlay for mobile -->
    <div
        x-show="mobileMenuOpen"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-background/80 z-30 lg:hidden"
        x-cloak
    ></div>
</div>
