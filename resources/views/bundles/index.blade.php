@extends('layouts.app')

@section('content')
    <section class="py-24 bg-background relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-cyan-500/5 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-12 space-y-4">
                <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-cyan-400 font-medium">
                    Products & Bundles
                </span>
                <h1 class="text-4xl md:text-6xl font-bold font-display">
                    Our <span class="gradient-text">Products</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
                    Browse our collection of premium 3D assets. <strong>Bundles</strong> are combinations of multiple products
                    offered at a discounted price, while <strong>single products</strong> are individual asset packs.
                </p>
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Filters Sidebar -->
                <aside class="lg:w-80 flex-shrink-0">
                    <div class="glass-card rounded-3xl p-6 sticky top-24">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold font-display">Filters</h2>
                            <button onclick="clearFilters()" class="text-sm text-cyan-400 hover:underline">Clear All</button>
                        </div>

                        <form id="filterForm" class="space-y-6">
                            <!-- Sort Order -->
                            <div>
                                <label class="block text-sm font-semibold mb-3">Sort By</label>
                                <select name="sort" id="sortFilter" class="w-full px-4 py-2 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none">
                                    <option value="default" {{ ($filters['sort'] ?? 'default') === 'default' ? 'selected' : '' }}>Default</option>
                                    <option value="price_low" {{ ($filters['sort'] ?? '') === 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ ($filters['sort'] ?? '') === 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="newest" {{ ($filters['sort'] ?? '') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div>
                                <label class="block text-sm font-semibold mb-3">Type</label>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="" {{ empty($filters['type'] ?? '') ? 'checked' : '' }} class="w-4 h-4 text-cyan-500 border-border focus:ring-cyan-500">
                                        <span class="text-sm text-muted-foreground">All</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="bundle" {{ ($filters['type'] ?? '') === 'bundle' ? 'checked' : '' }} class="w-4 h-4 text-cyan-500 border-border focus:ring-cyan-500">
                                        <span class="text-sm text-muted-foreground">Bundles Only</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="type" value="product" {{ ($filters['type'] ?? '') === 'product' ? 'checked' : '' }} class="w-4 h-4 text-cyan-500 border-border focus:ring-cyan-500">
                                        <span class="text-sm text-muted-foreground">Products Only</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-semibold mb-3">Price Range</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-muted-foreground mb-1">Min</label>
                                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" placeholder="${{ number_format($minPrice, 0) }}" step="0.01" min="0" class="w-full px-3 py-2 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-muted-foreground mb-1">Max</label>
                                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" placeholder="${{ number_format($maxPrice, 0) }}" step="0.01" min="0" class="w-full px-3 py-2 rounded-xl bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm">
                                    </div>
                                </div>
                                <button type="button" id="applyPriceFilter" class="w-full mt-3 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold text-sm hover:shadow-lg transition">
                                    Apply Price Filter
                                </button>
                            </div>

                            <!-- Categories -->
                            @if($categories->count() > 0)
                                <div>
                                    <label class="block text-sm font-semibold mb-3">Categories</label>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach($categories as $category)
                                            <label class="flex items-center gap-2 cursor-pointer hover:text-foreground transition">
                                                <input type="checkbox" name="category[]" value="{{ $category->id }}" {{ in_array($category->id, (array)($filters['category'] ?? [])) ? 'checked' : '' }} class="filter-checkbox w-4 h-4 text-cyan-500 border-border rounded focus:ring-cyan-500">
                                                <span class="text-sm text-muted-foreground">{{ $category->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Tags -->
                            @if($tags->count() > 0)
                                <div>
                                    <label class="block text-sm font-semibold mb-3">Tags</label>
                                    <div class="space-y-2 max-h-48 overflow-y-auto">
                                        @foreach($tags as $tag)
                                            <label class="flex items-center gap-2 cursor-pointer hover:text-foreground transition">
                                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, (array)($filters['tags'] ?? [])) ? 'checked' : '' }} class="filter-checkbox w-4 h-4 text-cyan-500 border-border rounded focus:ring-cyan-500">
                                                <span class="text-sm text-muted-foreground">{{ $tag->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </aside>

                <!-- Products Grid -->
                <div class="flex-1">
                    <!-- Toolbar -->
                    <div class="glass-card rounded-2xl p-4 mb-6">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <p class="text-sm text-muted-foreground">
                                    Showing <strong id="showingFrom">0</strong>-<strong id="showingTo">0</strong> of <strong id="showingTotal">0</strong> <span id="productText">products</span>
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3 flex-wrap">
                                <!-- Items per page -->
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-muted-foreground">Show:</label>
                                    <select name="per_page" id="perPageFilter" class="px-3 py-1.5 rounded-lg bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm">
                                        <option value="9" {{ $filters['per_page'] == 9 ? 'selected' : '' }}>9</option>
                                        <option value="12" {{ $filters['per_page'] == 12 ? 'selected' : '' }}>12</option>
                                        <option value="18" {{ $filters['per_page'] == 18 ? 'selected' : '' }}>18</option>
                                        <option value="24" {{ $filters['per_page'] == 24 ? 'selected' : '' }}>24</option>
                                        <option value="36" {{ $filters['per_page'] == 36 ? 'selected' : '' }}>36</option>
                                    </select>
                                </div>

                                <!-- Column count (only for grid view) -->
                                @if($filters['view'] === 'grid')
                                <div class="flex items-center gap-2">
                                    <label class="text-xs text-muted-foreground">Columns:</label>
                                    <div class="flex items-center gap-1 glass-card rounded-lg p-1">
                                        <button data-columns="2" class="column-btn p-1.5 rounded {{ $filters['columns'] == '2' ? 'bg-cyan-500/20 text-cyan-400' : 'text-muted-foreground hover:text-foreground' }}" title="2 Columns">
                                            <i data-lucide="columns-2" class="w-4 h-4"></i>
                                        </button>
                                        <button data-columns="3" class="column-btn p-1.5 rounded {{ $filters['columns'] == '3' ? 'bg-cyan-500/20 text-cyan-400' : 'text-muted-foreground hover:text-foreground' }}" title="3 Columns">
                                            <i data-lucide="columns-3" class="w-4 h-4"></i>
                                        </button>
                                        <button data-columns="4" class="column-btn p-1.5 rounded {{ $filters['columns'] == '4' ? 'bg-cyan-500/20 text-cyan-400' : 'text-muted-foreground hover:text-foreground' }}" title="4 Columns">
                                            <i data-lucide="columns-4" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif

                                <!-- View toggle -->
                                <div class="flex items-center gap-1 glass-card rounded-lg p-1">
                                    <button data-view="grid" class="view-btn p-1.5 rounded {{ $filters['view'] === 'grid' ? 'bg-cyan-500/20 text-cyan-400' : 'text-muted-foreground hover:text-foreground' }}" title="Grid View">
                                        <i data-lucide="grid-3x3" class="w-4 h-4"></i>
                                    </button>
                                    <button data-view="list" class="view-btn p-1.5 rounded {{ $filters['view'] === 'list' ? 'bg-cyan-500/20 text-cyan-400' : 'text-muted-foreground hover:text-foreground' }}" title="List View">
                                        <i data-lucide="list" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Container -->
                    <!-- Loading Spinner -->
                    <div id="loadingSpinner" class="hidden mb-12">
                        <div class="flex flex-col items-center justify-center py-32">
                            <div class="relative w-20 h-20">
                                <!-- Outer ring -->
                                <div class="absolute inset-0 rounded-full border-4 border-cyan-500/20"></div>
                                <!-- Spinning gradient ring -->
                                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-cyan-500 border-r-violet-500 animate-spin"></div>
                                <!-- Inner pulsing circle -->
                                <div class="absolute inset-2 rounded-full bg-gradient-to-br from-cyan-500/20 to-violet-500/20 animate-pulse"></div>
                            </div>
                            <p class="mt-8 text-muted-foreground font-medium animate-pulse">Loading products...</p>
                        </div>
                    </div>

                    <div id="productsContainer" class="mb-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3" data-view="grid" data-columns="3">
                        @forelse($products as $index => $product)
                            @php
                                $discount = $product->discount_percentage ?? ($product->original_price > 0 ? round((1 - $product->price / $product->original_price) * 100) : 0);
                            @endphp
                            
                            @if($filters['view'] === 'list')
                                <!-- List View -->
                                <div class="glass-card rounded-3xl overflow-hidden hover-lift group">
                                    <div class="flex flex-col md:flex-row gap-6 p-6">
                                        <div class="relative overflow-hidden rounded-2xl flex-shrink-0 w-full md:w-64 h-48">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                    <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                                                </div>
                                            @endif
                                            <div class="absolute top-4 left-4">
                                                @if($product->is_bundle)
                                                    <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                                        <i data-lucide="package" class="w-3 h-3"></i>
                                                        Bundle
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div>
                                                <h3 class="text-2xl font-bold font-display mb-2">{{ $product->title }}</h3>
                                                @if($product->description)
                                                    <p class="text-muted-foreground mb-4 line-clamp-3">{{ $product->description }}</p>
                                                @endif
                                                <div class="flex items-center gap-2 text-xs text-muted-foreground mb-4">
                                                    @if($product->file_size)
                                                        <span>{{ $product->file_size }}</span>
                                                        <span>•</span>
                                                    @endif
                                                    @if($product->file_count)
                                                        <span>{{ $product->file_count }} files</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="text-3xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                                                    @if($product->original_price)
                                                        <span class="text-muted-foreground line-through ml-2">${{ number_format($product->original_price, 2) }}</span>
                                                    @endif
                                                    @if($discount > 0)
                                                        <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full ml-3">
                                                            {{ $discount }}% OFF
                                                        </span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('bundles.show', $product->slug) }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                                    View {{ $product->is_bundle ? 'Bundle' : 'Product' }}
                                                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Grid View -->
                                <div class="glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                                    <div class="relative overflow-hidden">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            @if($product->is_bundle)
                                                <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                                    <i data-lucide="package" class="w-3 h-3"></i>
                                                    Bundle
                                                </span>
                                            @endif
                                        </div>
                                        <div class="absolute bottom-4 left-4 right-4">
                                            <h3 class="text-xl font-bold font-display mb-1">{{ $product->title }}</h3>
                                            @if($product->description)
                                                <p class="text-sm text-muted-foreground line-clamp-2">{{ $product->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="p-6 space-y-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <span class="text-3xl font-bold gradient-text">${{ number_format($product->price, 2) }}</span>
                                                @if($product->original_price)
                                                    <span class="text-muted-foreground line-through ml-2">${{ number_format($product->original_price, 2) }}</span>
                                                @endif
                                            </div>
                                            @if($discount > 0)
                                                <span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">
                                                    {{ $discount }}% OFF
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                            @if($product->file_size)
                                                <span>{{ $product->file_size }}</span>
                                                <span>•</span>
                                            @endif
                                            @if($product->file_count)
                                                <span>{{ $product->file_count }} files</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('bundles.show', $product->slug) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                            View {{ $product->is_bundle ? 'Bundle' : 'Product' }}
                                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col-span-full text-center py-16">
                                <div class="glass-card rounded-3xl p-8 max-w-md mx-auto">
                                    <i data-lucide="search-x" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
                                    <h3 class="text-xl font-bold font-display mb-2">No products found</h3>
                                    <p class="text-muted-foreground mb-4">Try adjusting your filters to see more results.</p>
                                    <button onclick="clearFilters()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold text-sm hover:shadow-lg transition">
                                        Clear Filters
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div id="paginationContainer" class="glass-card rounded-2xl p-4">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Page <strong id="currentPage">{{ $products->currentPage() }}</strong> of <strong id="lastPage">{{ $products->lastPage() }}</strong>
                                </div>
                                <div class="flex items-center gap-2" id="paginationButtons">
                                    @if($products->onFirstPage())
                                        <span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                        </span>
                                    @else
                                        <button onclick="loadProducts({{ $products->currentPage() - 1 }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                        </button>
                                    @endif

                                    @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                                        @if($page == $products->currentPage())
                                            <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <button onclick="loadProducts({{ $page }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                                                {{ $page }}
                                            </button>
                                        @endif
                                    @endforeach

                                    @if($products->hasMorePages())
                                        <button onclick="loadProducts({{ $products->currentPage() + 1 }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                        </button>
                                    @else
                                        <span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div id="paginationContainer" class="glass-card rounded-2xl p-4 hidden">
                            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                                <div class="text-sm text-muted-foreground">
                                    Page <strong id="currentPage">1</strong> of <strong id="lastPage">1</strong>
                                </div>
                                <div class="flex items-center gap-2" id="paginationButtons">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script>
        let currentPage = 1;
        let isLoading = false;
        const productsContainer = document.getElementById('productsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        
        // Initialize state from server
        const initialState = {
            view: '{{ $filters['view'] ?? 'grid' }}',
            columns: '{{ $filters['columns'] ?? '3' }}',
            per_page: {{ $filters['per_page'] ?? 12 }},
            sort: '{{ $filters['sort'] ?? 'default' }}',
            type: '{{ $filters['type'] ?? '' }}',
            category: @json($filters['category'] ?? []),
            tags: @json($filters['tags'] ?? []),
            min_price: '{{ $filters['min_price'] ?? '' }}',
            max_price: '{{ $filters['max_price'] ?? '' }}',
        };

        // Apply initial view and columns
        updateView(initialState.view);
        updateColumns(initialState.columns);

        // View toggle (CSS only)
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const view = btn.dataset.view;
                updateView(view);
            });
        });

        // Column toggle (CSS only)
        document.querySelectorAll('.column-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const columns = btn.dataset.columns;
                updateColumns(columns);
            });
        });

        function updateView(view) {
            initialState.view = view;
            productsContainer.dataset.view = view;
            
            // Update classes
            if (view === 'list') {
                productsContainer.classList.remove('grid', 'md:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4');
                productsContainer.classList.add('space-y-6');
                const columnBtn = document.querySelector('.column-btn');
                if (columnBtn && columnBtn.closest('div')) {
                    columnBtn.closest('div').style.display = 'none';
                }
            } else {
                productsContainer.classList.remove('space-y-6');
                productsContainer.classList.add('grid', 'gap-8', 'md:grid-cols-2');
                const columnBtn = document.querySelector('.column-btn');
                if (columnBtn && columnBtn.closest('div')) {
                    columnBtn.closest('div').style.display = 'flex';
                }
                updateColumns(initialState.columns);
            }
            
            // Update button states
            document.querySelectorAll('.view-btn').forEach(b => {
                if (b.dataset.view === view) {
                    b.classList.add('bg-cyan-500/20', 'text-cyan-400');
                    b.classList.remove('text-muted-foreground');
                } else {
                    b.classList.remove('bg-cyan-500/20', 'text-cyan-400');
                    b.classList.add('text-muted-foreground');
                }
            });
            
            // Re-render products with new view
            loadProducts();
        }

        function updateColumns(columns) {
            if (initialState.view === 'list') return;
            
            initialState.columns = columns;
            productsContainer.dataset.columns = columns;
            
            // Remove all column classes
            productsContainer.classList.remove('lg:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4');
            
            // Add new column class
            if (columns === '2') {
                productsContainer.classList.add('lg:grid-cols-2');
            } else if (columns === '4') {
                productsContainer.classList.add('lg:grid-cols-4');
            } else {
                productsContainer.classList.add('lg:grid-cols-3');
            }
            
            // Update button states
            document.querySelectorAll('.column-btn').forEach(b => {
                if (b.dataset.columns === columns) {
                    b.classList.add('bg-cyan-500/20', 'text-cyan-400');
                    b.classList.remove('text-muted-foreground');
                } else {
                    b.classList.remove('bg-cyan-500/20', 'text-cyan-400');
                    b.classList.add('text-muted-foreground');
                }
            });
        }

        // Filter event listeners
        document.getElementById('sortFilter')?.addEventListener('change', (e) => {
            initialState.sort = e.target.value;
            currentPage = 1;
            loadProducts();
        });

        document.getElementById('perPageFilter')?.addEventListener('change', (e) => {
            initialState.per_page = parseInt(e.target.value);
            currentPage = 1;
            loadProducts();
        });

        document.querySelectorAll('input[name="type"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                initialState.type = e.target.value;
                currentPage = 1;
                loadProducts();
            });
        });

        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const name = checkbox.name;
                const value = parseInt(checkbox.value);
                
                if (name === 'category[]') {
                    if (checkbox.checked) {
                        if (!initialState.category.includes(value)) {
                            initialState.category.push(value);
                        }
                    } else {
                        initialState.category = initialState.category.filter(id => id !== value);
                    }
                } else if (name === 'tags[]') {
                    if (checkbox.checked) {
                        if (!initialState.tags.includes(value)) {
                            initialState.tags.push(value);
                        }
                    } else {
                        initialState.tags = initialState.tags.filter(id => id !== value);
                    }
                }
                
                currentPage = 1;
                loadProducts();
            });
        });

        document.getElementById('applyPriceFilter')?.addEventListener('click', () => {
            const minPrice = document.querySelector('input[name="min_price"]').value;
            const maxPrice = document.querySelector('input[name="max_price"]').value;
            initialState.min_price = minPrice;
            initialState.max_price = maxPrice;
            currentPage = 1;
            loadProducts();
        });

        // Load products via AJAX
        function loadProducts(page = null) {
            if (isLoading) return;
            
            isLoading = true;
            if (page) currentPage = page;
            
            // Show loader, hide products completely
            const loader = document.getElementById('loadingSpinner');
            const container = document.getElementById('productsContainer');
            if (loader) loader.classList.remove('hidden');
            if (container) container.classList.add('hidden');
            
            const params = new URLSearchParams({
                page: currentPage,
                per_page: initialState.per_page,
                sort: initialState.sort,
                view: initialState.view,
                columns: initialState.columns,
                ...(initialState.type && { type: initialState.type }),
                ...(initialState.min_price && { min_price: initialState.min_price }),
                ...(initialState.max_price && { max_price: initialState.max_price }),
            });
            
            initialState.category.forEach(id => params.append('category[]', id));
            initialState.tags.forEach(id => params.append('tags[]', id));
            
            fetch('{{ route('bundles.index') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                renderProducts(data.products);
                updatePagination(data.pagination);
                updateProductCount(data.pagination);
                isLoading = false;
                
                // Hide loader, show products
                const loader = document.getElementById('loadingSpinner');
                const container = document.getElementById('productsContainer');
                if (loader) loader.classList.add('hidden');
                if (container) container.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                isLoading = false;
                
                // Hide loader, show products even on error
                const loader = document.getElementById('loadingSpinner');
                const container = document.getElementById('productsContainer');
                if (loader) loader.classList.add('hidden');
                if (container) container.classList.remove('hidden');
            });
        }

        function renderProducts(products) {
            const container = productsContainer;
            container.innerHTML = '';
            
            if (products.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <div class="glass-card rounded-3xl p-8 max-w-md mx-auto">
                            <i data-lucide="search-x" class="w-16 h-16 text-muted-foreground mx-auto mb-4"></i>
                            <h3 class="text-xl font-bold font-display mb-2">No products found</h3>
                            <p class="text-muted-foreground mb-4">Try adjusting your filters to see more results.</p>
                            <button onclick="window.clearFilters()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold text-sm hover:shadow-lg transition">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                `;
                lucide.createIcons();
                return;
            }
            
            products.forEach((product, index) => {
                const discount = product.original_price > 0 
                    ? Math.round((1 - product.price / product.original_price) * 100) 
                    : 0;
                
                const imageUrl = product.image_url || '';
                const imageHtml = imageUrl 
                    ? `<img src="${imageUrl}" alt="${product.title}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">`
                    : `<div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                        <i data-lucide="package" class="w-16 h-16 text-cyan-400/50"></i>
                    </div>`;
                
                if (initialState.view === 'list') {
                    container.innerHTML += `
                        <div class="glass-card rounded-3xl overflow-hidden hover-lift group">
                            <div class="flex flex-col md:flex-row gap-6 p-6">
                                <div class="relative overflow-hidden rounded-2xl flex-shrink-0 w-full md:w-64 h-48">
                                    ${imageHtml}
                                    ${product.is_bundle ? `<div class="absolute top-4 left-4">
                                        <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                            <i data-lucide="package" class="w-3 h-3"></i> Bundle
                                        </span>
                                    </div>` : ''}
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold font-display mb-2">${product.title}</h3>
                                        ${product.description ? `<p class="text-muted-foreground mb-4 line-clamp-3">${product.description}</p>` : ''}
                                        <div class="flex items-center gap-2 text-xs text-muted-foreground mb-4">
                                            ${product.file_size ? `<span>${product.file_size}</span><span>•</span>` : ''}
                                            ${product.file_count ? `<span>${product.file_count} files</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <span class="text-3xl font-bold gradient-text">$${parseFloat(product.price).toFixed(2)}</span>
                                            ${product.original_price ? `<span class="text-muted-foreground line-through ml-2">$${parseFloat(product.original_price).toFixed(2)}</span>` : ''}
                                            ${discount > 0 ? `<span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full ml-3">${discount}% OFF</span>` : ''}
                                        </div>
                                        <a href="/bundles/${product.slug}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                            View ${product.is_bundle ? 'Bundle' : 'Product'}
                                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    container.innerHTML += `
                        <div class="glass-card rounded-3xl overflow-hidden hover-lift group animate-slide-in-up" style="animation-delay: ${index * 0.1}s">
                            <div class="relative overflow-hidden">
                                ${imageHtml}
                                <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                                ${product.is_bundle ? `<div class="absolute top-4 left-4 flex flex-col gap-2">
                                    <span class="bg-violet-500/20 text-violet-400 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1">
                                        <i data-lucide="package" class="w-3 h-3"></i> Bundle
                                    </span>
                                </div>` : ''}
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="text-xl font-bold font-display mb-1">${product.title}</h3>
                                    ${product.description ? `<p class="text-sm text-muted-foreground line-clamp-2">${product.description}</p>` : ''}
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-3xl font-bold gradient-text">$${parseFloat(product.price).toFixed(2)}</span>
                                        ${product.original_price ? `<span class="text-muted-foreground line-through ml-2">$${parseFloat(product.original_price).toFixed(2)}</span>` : ''}
                                    </div>
                                    ${discount > 0 ? `<span class="bg-cyan-500/20 text-cyan-400 text-xs font-bold px-3 py-1 rounded-full">${discount}% OFF</span>` : ''}
                                </div>
                                <div class="flex items-center gap-2 text-xs text-muted-foreground">
                                    ${product.file_size ? `<span>${product.file_size}</span><span>•</span>` : ''}
                                    ${product.file_count ? `<span>${product.file_count} files</span>` : ''}
                                </div>
                                <a href="/bundles/${product.slug}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                    View ${product.is_bundle ? 'Bundle' : 'Product'}
                                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                </a>
                            </div>
                        </div>
                    `;
                }
            });
            
            lucide.createIcons();
        }

        function updatePagination(pagination) {
            if (!pagination.has_more && pagination.current_page === 1) {
                paginationContainer.classList.add('hidden');
                return;
            }
            
            paginationContainer.classList.remove('hidden');
            document.getElementById('currentPage').textContent = pagination.current_page;
            document.getElementById('lastPage').textContent = pagination.last_page;
            
            const buttonsContainer = document.getElementById('paginationButtons');
            buttonsContainer.innerHTML = '';
            
            // Previous button
            if (pagination.current_page === 1) {
                buttonsContainer.innerHTML += `<span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </span>`;
            } else {
                buttonsContainer.innerHTML += `<button onclick="loadProducts(${pagination.current_page - 1})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                </button>`;
            }
            
            // Page numbers
            const startPage = Math.max(1, pagination.current_page - 2);
            const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                if (i === pagination.current_page) {
                    buttonsContainer.innerHTML += `<span class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold">${i}</span>`;
                } else {
                    buttonsContainer.innerHTML += `<button onclick="loadProducts(${i})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">${i}</button>`;
                }
            }
            
            // Next button
            if (pagination.has_more) {
                buttonsContainer.innerHTML += `<button onclick="loadProducts(${pagination.current_page + 1})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>`;
            } else {
                buttonsContainer.innerHTML += `<span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>`;
            }
            
            lucide.createIcons();
        }

        function updateProductCount(pagination) {
            document.getElementById('showingFrom').textContent = pagination.from || 0;
            document.getElementById('showingTo').textContent = pagination.to || 0;
            document.getElementById('showingTotal').textContent = pagination.total || 0;
            document.getElementById('productText').textContent = pagination.total === 1 ? 'product' : 'products';
        }

        window.clearFilters = function() {
            initialState.view = 'grid';
            initialState.columns = '3';
            initialState.per_page = 12;
            initialState.sort = 'default';
            initialState.type = '';
            initialState.category = [];
            initialState.tags = [];
            initialState.min_price = '';
            initialState.max_price = '';
            currentPage = 1;
            
            // Reset form
            document.getElementById('filterForm').reset();
            document.querySelector('input[name="type"][value=""]').checked = true;
            
            updateView('grid');
            updateColumns('3');
            loadProducts();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial pagination state
            @if($products->hasPages())
                currentPage = {{ $products->currentPage() }};
            @endif
        });
    </script>
@endsection

