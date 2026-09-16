@extends('layouts.app')

@push('head')
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} Blog RSS" href="{{ route('blog.feed') }}">
@endpush

@section('content')
    <section class="py-24 bg-background relative overflow-hidden">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[400px] bg-gradient-to-b from-violet-500/5 to-transparent rounded-full blur-3xl"></div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 space-y-4">
                <span class="inline-block glass-card px-4 py-2 rounded-full text-sm text-violet-400 font-medium">
                    Latest Articles
                </span>
                <h1 class="text-4xl md:text-5xl font-bold font-display">
                    Our <span class="gradient-text">Blog</span>
                </h1>
                <p class="text-xl text-muted-foreground max-w-2xl mx-auto">
                    Tips, tutorials, and insights about 3D design, SketchUp, and interior design trends.
                </p>

                {{-- Archive context (category / tag pages) --}}
                @if(!empty($activeCategory) || !empty($activeTag))
                    <nav aria-label="Breadcrumb" class="mt-6">
                        <ol class="flex items-center justify-center gap-2 text-sm text-muted-foreground">
                            <li><a href="{{ url('/') }}" class="hover:text-foreground transition">Home</a></li>
                            <li aria-hidden="true">/</li>
                            <li><a href="{{ url('/blog') }}" class="hover:text-foreground transition">Blog</a></li>
                            <li aria-hidden="true">/</li>
                            <li aria-current="page" class="text-foreground">
                                {{ !empty($activeCategory) ? $activeCategory->name : 'Tag: ' . $activeTag->name }}
                            </li>
                        </ol>
                    </nav>
                    @if(!empty($activeCategory) && $activeCategory->description)
                        <p class="text-muted-foreground max-w-2xl mx-auto mt-4">{{ $activeCategory->description }}</p>
                    @endif
                @endif
            </div>

            @if($posts->isEmpty())
                <div class="text-center py-16">
                    <i data-lucide="file-text" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                    <h2 class="text-2xl font-bold font-display mb-2">No Posts Yet</h2>
                    <p class="text-muted-foreground">Check back soon for new blog posts!</p>
                </div>
            @else
                <!-- Toolbar -->
                <div class="glass-card rounded-2xl p-4 mb-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-muted-foreground">
                                Showing <strong id="showingFrom">0</strong>-<strong id="showingTo">0</strong> of <strong id="showingTotal">0</strong> <span id="postText">posts</span>
                            </p>
                        </div>
                        
                        <div class="flex items-center gap-3 flex-wrap">
                            <!-- Search -->
                            <div class="flex items-center gap-2">
                                <input type="text" id="searchInput" value="{{ $filters['q'] ?? '' }}" placeholder="Search articles..."
                                    class="px-3 py-1.5 rounded-lg bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm w-44 md:w-56" />
                                <button id="searchBtn" class="px-3 py-1.5 rounded-lg bg-cyan-500/20 text-cyan-400 text-sm font-medium hover:bg-cyan-500/30 transition">
                                    Search
                                </button>
                            </div>
                            <!-- Sort -->
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-muted-foreground">Sort:</label>
                                <select name="sort" id="sortFilter" class="px-3 py-1.5 rounded-lg bg-card border border-border focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/30 outline-none text-sm">
                                    <option value="newest" {{ $filters['sort'] == 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ $filters['sort'] == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="title_asc" {{ $filters['sort'] == 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                                    <option value="title_desc" {{ $filters['sort'] == 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                                </select>
                            </div>

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

                <!-- Posts Container -->
                <div id="postsContainer" class="mb-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3" data-view="grid" data-columns="3">
                    @foreach($posts as $index => $post)
                        @if($filters['view'] === 'list')
                            <!-- List View -->
                            <a href="{{ url('/blog/' . $post->slug) }}" class="glass-card rounded-3xl overflow-hidden hover-lift group block">
                                <div class="flex flex-col md:flex-row gap-6 p-6">
                                    <div class="relative overflow-hidden rounded-2xl flex-shrink-0 w-full md:w-64 h-48">
                                        @if($post->featured_image_url)
                                            <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt_text }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                        @else
                                            <div class="w-full h-full bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                                <i data-lucide="file-text" class="w-16 h-16 text-cyan-400/50"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h3 class="text-2xl font-bold font-display mb-2">{{ $post->title }}</h3>
                                            @if($post->excerpt)
                                                <p class="text-muted-foreground mb-4 line-clamp-3">{{ $post->excerpt }}</p>
                                            @endif
                                            <div class="flex items-center gap-4 text-sm text-muted-foreground mb-4">
                                                @if($post->user)
                                                    <span class="flex items-center gap-1">
                                                        <i data-lucide="user" class="w-4 h-4"></i> {{ $post->user->name }}
                                                    </span>
                                                @endif
                                                @if($post->published_at)
                                                    <span class="flex items-center gap-1">
                                                        <i data-lucide="calendar" class="w-4 h-4"></i> {{ $post->published_at->format('M d, Y') }}
                                                    </span>
                                                @endif
                                                <span class="flex items-center gap-1">
                                                    <i data-lucide="clock" class="w-4 h-4"></i> {{ $post->reading_time }} min
                                                </span>
                                            </div>
                                        </div>
                                        <div class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn w-fit">
                                            Read More
                                            <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @else
                            <!-- Grid View -->
                            <a href="{{ url('/blog/' . $post->slug) }}" class="glass-card rounded-3xl overflow-hidden hover-lift group block animate-slide-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                                <div class="relative overflow-hidden">
                                    @if($post->featured_image_url)
                                        <img src="{{ $post->featured_image_url }}" alt="{{ $post->featured_image_alt_text }}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                                            <i data-lucide="file-text" class="w-16 h-16 text-cyan-400/50"></i>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                                    <div class="absolute bottom-4 left-4 right-4">
                                        <h3 class="text-xl font-bold font-display mb-2 line-clamp-2">{{ $post->title }}</h3>
                                        @if($post->excerpt)
                                            <p class="text-sm text-muted-foreground line-clamp-2">{{ $post->excerpt }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                        @if($post->user)
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="user" class="w-4 h-4"></i> {{ $post->user->name }}
                                            </span>
                                        @endif
                                        @if($post->published_at)
                                            <span class="flex items-center gap-1">
                                                <i data-lucide="calendar" class="w-4 h-4"></i> {{ $post->published_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                        Read More
                                        <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($posts->hasPages())
                    <div id="paginationContainer" class="glass-card rounded-2xl p-4">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-muted-foreground">
                                Page <strong id="currentPage">{{ $posts->currentPage() }}</strong> of <strong id="lastPage">{{ $posts->lastPage() }}</strong>
                            </div>
                            <div class="flex items-center gap-2" id="paginationButtons">
                                @if($posts->onFirstPage())
                                    <span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                    </span>
                                @else
                                    <button onclick="loadPosts({{ $posts->currentPage() - 1 }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                    </button>
                                @endif

                                @foreach($posts->getUrlRange(max(1, $posts->currentPage() - 2), min($posts->lastPage(), $posts->currentPage() + 2)) as $page => $url)
                                    @if($page == $posts->currentPage())
                                        <span class="px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button onclick="loadPosts({{ $page }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach

                                @if($posts->hasMorePages())
                                    <button onclick="loadPosts({{ $posts->currentPage() + 1 }})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
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
            @endif
        </div>
    </section>

    <script>
        let currentPage = 1;
        let isLoading = false;
        const postsContainer = document.getElementById('postsContainer');
        const paginationContainer = document.getElementById('paginationContainer');
        
        // Initialize state from server
        const initialState = {
            view: '{{ $filters['view'] ?? 'grid' }}',
            columns: '{{ $filters['columns'] ?? '3' }}',
            per_page: {{ $filters['per_page'] ?? 12 }},
            sort: '{{ $filters['sort'] ?? 'newest' }}',
            q: @json($filters['q'] ?? ''),
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
            postsContainer.dataset.view = view;
            
            // Update classes
            if (view === 'list') {
                postsContainer.classList.remove('grid', 'md:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4');
                postsContainer.classList.add('space-y-6');
                document.querySelector('.column-btn')?.closest('div').style.display = 'none';
            } else {
                postsContainer.classList.remove('space-y-6');
                postsContainer.classList.add('grid', 'gap-8', 'md:grid-cols-2');
                document.querySelector('.column-btn')?.closest('div').style.display = 'flex';
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
            
            // Re-render posts with new view
            loadPosts();
        }

        function updateColumns(columns) {
            if (initialState.view === 'list') return;
            
            initialState.columns = columns;
            postsContainer.dataset.columns = columns;
            
            // Remove all column classes
            postsContainer.classList.remove('lg:grid-cols-2', 'lg:grid-cols-3', 'lg:grid-cols-4');
            
            // Add new column class
            if (columns === '2') {
                postsContainer.classList.add('lg:grid-cols-2');
            } else if (columns === '4') {
                postsContainer.classList.add('lg:grid-cols-4');
            } else {
                postsContainer.classList.add('lg:grid-cols-3');
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

        // Search
        const searchInput = document.getElementById('searchInput');
        const runSearch = () => {
            initialState.q = searchInput.value.trim();
            currentPage = 1;
            loadPosts();
        };
        document.getElementById('searchBtn')?.addEventListener('click', runSearch);
        searchInput?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                runSearch();
            }
        });

        // Filter event listeners
        document.getElementById('sortFilter')?.addEventListener('change', (e) => {
            initialState.sort = e.target.value;
            currentPage = 1;
            loadPosts();
        });

        document.getElementById('perPageFilter')?.addEventListener('change', (e) => {
            initialState.per_page = parseInt(e.target.value);
            currentPage = 1;
            loadPosts();
        });

        // Load posts via AJAX
        function loadPosts(page = null) {
            if (isLoading) return;
            
            isLoading = true;
            if (page) currentPage = page;
            
            const params = new URLSearchParams({
                page: currentPage,
                per_page: initialState.per_page,
                sort: initialState.sort,
                q: initialState.q,
                view: initialState.view,
                columns: initialState.columns,
            });
            
            fetch('{{ route('blog.index') }}?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                renderPosts(data.posts);
                updatePagination(data.pagination);
                updatePostCount(data.pagination);
                isLoading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                isLoading = false;
            });
        }

        function renderPosts(posts) {
            const container = postsContainer;
            container.innerHTML = '';
            
            if (posts.length === 0) {
                container.innerHTML = `
                    <div class="col-span-full text-center py-16">
                        <i data-lucide="file-text" class="w-16 h-16 mx-auto mb-4 text-muted-foreground"></i>
                        <h2 class="text-2xl font-bold font-display mb-2">No Posts Yet</h2>
                        <p class="text-muted-foreground">Check back soon for new blog posts!</p>
                    </div>
                `;
                lucide.createIcons();
                return;
            }
            
            posts.forEach((post, index) => {
                const imageUrl = post.featured_image_url || '';
                const imageHtml = imageUrl 
                    ? `<img src="${imageUrl}" alt="${post.title}" class="w-full h-64 object-cover transition-transform duration-700 group-hover:scale-110">`
                    : `<div class="w-full h-64 bg-gradient-to-br from-cyan-500/20 to-violet-500/20 flex items-center justify-center">
                        <i data-lucide="file-text" class="w-16 h-16 text-cyan-400/50"></i>
                    </div>`;
                
                const publishedDate = post.published_at ? new Date(post.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : '';
                
                if (initialState.view === 'list') {
                    container.innerHTML += `
                        <a href="/blog/${post.slug}" class="glass-card rounded-3xl overflow-hidden hover-lift group block">
                            <div class="flex flex-col md:flex-row gap-6 p-6">
                                <div class="relative overflow-hidden rounded-2xl flex-shrink-0 w-full md:w-64 h-48">
                                    ${imageHtml}
                                </div>
                                <div class="flex-1 flex flex-col justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold font-display mb-2">${post.title}</h3>
                                        ${post.excerpt ? `<p class="text-muted-foreground mb-4 line-clamp-3">${post.excerpt}</p>` : ''}
                                        <div class="flex items-center gap-4 text-sm text-muted-foreground mb-4">
                                            ${post.user ? `<span class="flex items-center gap-1"><i data-lucide="user" class="w-4 h-4"></i> ${post.user.name}</span>` : ''}
                                            ${publishedDate ? `<span class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> ${publishedDate}</span>` : ''}
                                        </div>
                                    </div>
                                    <div class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn w-fit">
                                        Read More
                                        <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                    </div>
                                </div>
                            </div>
                        </a>
                    `;
                } else {
                    container.innerHTML += `
                        <a href="/blog/${post.slug}" class="glass-card rounded-3xl overflow-hidden hover-lift group block animate-slide-in-up" style="animation-delay: ${index * 0.1}s">
                            <div class="relative overflow-hidden">
                                ${imageHtml}
                                <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/20 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4">
                                    <h3 class="text-xl font-bold font-display mb-2 line-clamp-2">${post.title}</h3>
                                    ${post.excerpt ? `<p class="text-sm text-muted-foreground line-clamp-2">${post.excerpt}</p>` : ''}
                                </div>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                    ${post.user ? `<span class="flex items-center gap-1"><i data-lucide="user" class="w-4 h-4"></i> ${post.user.name}</span>` : ''}
                                    ${publishedDate ? `<span class="flex items-center gap-1"><i data-lucide="calendar" class="w-4 h-4"></i> ${publishedDate}</span>` : ''}
                                </div>
                                <div class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-violet-500 text-background font-semibold shadow-lg hover:shadow-xl transition group/btn">
                                    Read More
                                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover/btn:translate-x-1"></i>
                                </div>
                            </div>
                        </a>
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
                buttonsContainer.innerHTML += `<button onclick="loadPosts(${pagination.current_page - 1})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
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
                    buttonsContainer.innerHTML += `<button onclick="loadPosts(${i})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">${i}</button>`;
                }
            }
            
            // Next button
            if (pagination.has_more) {
                buttonsContainer.innerHTML += `<button onclick="loadPosts(${pagination.current_page + 1})" class="px-4 py-2 rounded-lg bg-card border border-border hover:border-cyan-500 hover:text-cyan-400 transition">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </button>`;
            } else {
                buttonsContainer.innerHTML += `<span class="px-4 py-2 rounded-lg bg-card border border-border text-muted-foreground cursor-not-allowed">
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </span>`;
            }
            
            lucide.createIcons();
        }

        function updatePostCount(pagination) {
            document.getElementById('showingFrom').textContent = pagination.from || 0;
            document.getElementById('showingTo').textContent = pagination.to || 0;
            document.getElementById('showingTotal').textContent = pagination.total || 0;
            document.getElementById('postText').textContent = pagination.total === 1 ? 'post' : 'posts';
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Set initial pagination state
            @if($posts->hasPages())
                currentPage = {{ $posts->currentPage() }};
            @endif
        });
    </script>
@endsection

