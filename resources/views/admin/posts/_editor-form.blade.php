@php
$post = $post ?? null;
$isEditMode = $isEditMode ?? false;
@endphp
@php
$postData = null;
if ($post) {
    $post->load('tags');
    $postData = [
        'id' => $post->id,
        'title' => $post->title,
        'slug' => $post->slug,
        'excerpt' => $post->excerpt,
        'content' => $post->content,
        'featured_image' => $post->featured_image,
        'category_id' => $post->category_id,
        'status' => $post->status,
        'tags' => $post->tags->map(fn($tag) => ['id' => $tag->id])->toArray(),
        'meta_title' => $post->meta_title,
        'meta_description' => $post->meta_description,
        'focus_keyword' => $post->focus_keyword,
        'canonical_url' => $post->canonical_url,
        'robots_index' => $post->robots_index ?? 'index',
        'robots_follow' => $post->robots_follow ?? 'follow',
        'featured_image_alt' => $post->featured_image_alt,
        'og_title' => $post->og_title,
        'og_description' => $post->og_description,
        'og_image' => $post->og_image,
        'twitter_card' => $post->twitter_card,
        'twitter_title' => $post->twitter_title,
        'twitter_description' => $post->twitter_description,
        'twitter_image' => $post->twitter_image,
        'schema_markup' => is_array($post->schema_markup) ? json_encode($post->schema_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $post->schema_markup,
    ];
}
@endphp

<div class="p-6 md:p-8" x-data="postEditor({{ $isEditMode ? 'true' : 'false' }}, @js($postData), @js($categories->toArray()), @js($tags->toArray()))" x-init="init()">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.posts.index') }}">
            <button class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2" data-testid="button-back">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-foreground" data-testid="text-editor-title">
                {{ $isEditMode ? 'Edit Post' : 'New Post' }}
            </h1>
            <p class="text-muted-foreground">
                {{ $isEditMode ? 'Modify your post content' : 'Create a new post for your blog' }}
            </p>
        </div>
    </div>

    <form @submit.prevent="savePost()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Editor -->
        <div class="lg:col-span-2 space-y-6">
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Title *</label>
                        <input
                            type="text"
                            x-model="title"
                            @input="if (!isEditMode) slug = generateSlug(title)"
                            placeholder="Post title"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="input-title"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Slug *</label>
                        <input
                            type="text"
                            x-model="slug"
                            placeholder="slug-del-post"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="input-slug"
                            required
                        />
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Excerpt</label>
                        <textarea
                            x-model="excerpt"
                            placeholder="Brief description of the post"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="input-excerpt"
                        ></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Content *</label>
                        <div x-ref="editorContainer" class="bg-background rounded-md border border-input">
                            <div id="content-editor" style="min-height: 400px;"></div>
                        </div>
                        <textarea
                            x-model="content"
                            id="content-hidden"
                            style="display: none;"
                            data-testid="input-content"
                            required
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO (WordPress-style) -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm" x-data="{ seoOpen: false }">
                <div class="p-6 flex items-center justify-between cursor-pointer" @click="seoOpen = !seoOpen">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">SEO Settings</h3>
                    <svg class="w-5 h-5 transition-transform" :class="seoOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
                <div class="p-6 pt-0 space-y-4" x-show="seoOpen">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">SEO Title</label>
                            <input type="text" x-model="seo.meta_title" placeholder="Defaults to post title"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Focus Keyword</label>
                            <input type="text" x-model="seo.focus_keyword" placeholder="e.g., sketchup kitchen models"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Meta Description</label>
                        <textarea x-model="seo.meta_description" rows="2" placeholder="Defaults to excerpt (max ~155 chars)"
                            class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Canonical URL</label>
                            <input type="text" x-model="seo.canonical_url" placeholder="Defaults to post URL"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Robots Index</label>
                            <select x-model="seo.robots_index" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="index">index</option>
                                <option value="noindex">noindex</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Robots Follow</label>
                            <select x-model="seo.robots_follow" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                <option value="follow">follow</option>
                                <option value="nofollow">nofollow</option>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Featured Image Alt Text</label>
                        <input type="text" x-model="seo.featured_image_alt" placeholder="Describe the featured image"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                    </div>
                    <div class="border-t border-border pt-4">
                        <p class="text-sm font-medium mb-3">Social / Open Graph</p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">OG Title</label>
                                <input type="text" x-model="seo.og_title" placeholder="Defaults to SEO title"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">OG Image URL</label>
                                <input type="text" x-model="seo.og_image" placeholder="Defaults to featured image"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="space-y-2 mt-4">
                            <label class="text-sm font-medium">OG Description</label>
                            <textarea x-model="seo.og_description" rows="2"
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="grid md:grid-cols-3 gap-4 mt-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Twitter Card</label>
                                <select x-model="seo.twitter_card" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option value="">Default</option>
                                    <option value="summary">summary</option>
                                    <option value="summary_large_image">summary_large_image</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Twitter Title</label>
                                <input type="text" x-model="seo.twitter_title" placeholder="Defaults to OG title"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Twitter Image URL</label>
                                <input type="text" x-model="seo.twitter_image" placeholder="Defaults to OG image"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm" />
                            </div>
                        </div>
                        <div class="space-y-2 mt-4">
                            <label class="text-sm font-medium">Twitter Description</label>
                            <textarea x-model="seo.twitter_description" rows="2"
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>
                    <div class="border-t border-border pt-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Custom Schema Markup (JSON-LD)</label>
                            <textarea x-model="seo.schema_markup" rows="4" placeholder='{"@context": "https://schema.org", ...}'
                                class="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm font-mono"></textarea>
                            <p class="text-xs text-muted-foreground">Optional. Overrides the auto-generated BlogPosting schema.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Publish</h3>
                </div>
                <div class="p-6 pt-0 space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Status</label>
                        <select
                            x-model="status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="select-status"
                        >
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Category</label>
                        <select
                            x-model="categoryId"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="select-category"
                        >
                            <option value="">Select a category</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>

                    <p class="text-xs text-muted-foreground">Reading time is calculated automatically from the content.</p>

                    <button
                        type="submit"
                        :disabled="saving"
                        class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                        data-testid="button-save"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="saving ? 'Saving...' : 'Save Post'"></span>
                    </button>
                </div>
            </div>

            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Featured Image</h3>
                </div>
                <div class="p-6 pt-0">
                    <input
                        type="text"
                        x-model="featuredImage"
                        placeholder="Image URL"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-featured-image"
                    />
                    <template x-if="featuredImage">
                        <img :src="featuredImage" alt="Preview" class="mt-4 rounded-lg w-full" />
                    </template>
                </div>
            </div>

            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Tags</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in tags" :key="tag.id">
                            <span
                                @click="toggleTag(tag.id)"
                                class="px-2 py-1 rounded text-sm cursor-pointer transition-colors"
                                :class="selectedTags.includes(tag.id) ? 'bg-primary text-primary-foreground' : 'bg-secondary text-secondary-foreground border border-input'"
                                x-text="tag.name"
                                :data-testid="'tag-' + tag.id"
                            ></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function postEditor(isEdit, postData, categoriesData, tagsData) {
    return {
        isEditMode: isEdit,
        postId: postData?.id || null,
        title: postData?.title || '',
        slug: postData?.slug || '',
        excerpt: postData?.excerpt || '',
        content: postData?.content || '',
        featuredImage: postData?.featured_image || '',
        categoryId: postData?.category_id?.toString() || '',
        selectedTags: postData?.tags?.map(t => t.id) || [],
        status: postData?.status || 'draft',
        // SEO fields (WordPress-style)
        seo: {
            meta_title: postData?.meta_title || '',
            meta_description: postData?.meta_description || '',
            focus_keyword: postData?.focus_keyword || '',
            canonical_url: postData?.canonical_url || '',
            robots_index: postData?.robots_index || 'index',
            robots_follow: postData?.robots_follow || 'follow',
            featured_image_alt: postData?.featured_image_alt || '',
            og_title: postData?.og_title || '',
            og_description: postData?.og_description || '',
            og_image: postData?.og_image || '',
            twitter_card: postData?.twitter_card || '',
            twitter_title: postData?.twitter_title || '',
            twitter_description: postData?.twitter_description || '',
            twitter_image: postData?.twitter_image || '',
            schema_markup: postData?.schema_markup || '',
        },
        categories: categoriesData,
        tags: tagsData,
        saving: false,
        quill: null,
        
        init() {
            // Initialize Quill editor after Alpine.js is ready
            this.$nextTick(() => {
                this.initEditor();
            });
        },
        
        initEditor() {
            // Check if editor already exists to prevent double initialization
            if (this.quill || this.editorInitialized) {
                return;
            }
            
            const self = this;
            const editorContainer = document.getElementById('content-editor');
            
            if (!editorContainer) {
                console.error('Editor container not found');
                return;
            }
            
            // Check if Quill is already initialized - look for ql-toolbar in the document
            if (document.querySelector('#content-editor + .ql-toolbar') || 
                document.querySelector('.ql-toolbar[data-quill-id]')) {
                console.log('Quill toolbar already exists, skipping');
                this.editorInitialized = true;
                return;
            }
            
            // Mark as initializing
            this.editorInitialized = true;
            
            // Clear any existing content to prevent conflicts
            editorContainer.innerHTML = '';
            
            // Initialize Quill editor
            this.quill = new Quill(editorContainer, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'align': [] }],
                        ['link', 'image', 'video'],
                        ['blockquote', 'code-block'],
                        ['clean']
                    ]
                },
                placeholder: 'Escribe el contenido de tu post aquí...',
            });
            
            // Set initial content if editing
            if (this.content) {
                this.quill.root.innerHTML = this.content;
            }
            
            // Sync Quill content with Alpine.js model
            this.quill.on('text-change', function() {
                const html = self.quill.root.innerHTML;
                self.content = html;
                // Update hidden textarea for form validation
                const hiddenTextarea = document.getElementById('content-hidden');
                if (hiddenTextarea) {
                    hiddenTextarea.value = html;
                }
            });
        },
        
        generateSlug(text) {
            return text
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        },
        
        toggleTag(tagId) {
            if (this.selectedTags.includes(tagId)) {
                this.selectedTags = this.selectedTags.filter(id => id !== tagId);
            } else {
                this.selectedTags.push(tagId);
            }
        },
        
        async savePost() {
            // Get content from Quill editor if it exists
            if (this.quill) {
                this.content = this.quill.root.innerHTML;
            }
            
            if (!this.title || !this.slug || !this.content) {
                alert('Please complete all required fields');
                return;
            }
            
            this.saving = true;
            const url = this.isEditMode 
                ? `/admin/posts/${this.postId}`
                : '/admin/posts';
            
            try {
                const formData = new FormData();
                formData.append('title', this.title);
                formData.append('slug', this.slug);
                formData.append('excerpt', this.excerpt);
                formData.append('content', this.content);
                formData.append('featured_image', this.featuredImage);
                formData.append('category_id', this.categoryId || '');
                formData.append('status', this.status);
                formData.append('tagIds', JSON.stringify(this.selectedTags));
                // SEO fields
                for (const [key, value] of Object.entries(this.seo)) {
                    formData.append(key, value ?? '');
                }
                if (this.isEditMode) {
                    formData.append('_method', 'PUT');
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData,
                });
                
                if (response.ok) {
                    window.location.href = '/admin/posts';
                } else {
                    const error = await response.json();
                    alert(error.message || 'Error saving the post');
                }
            } catch (error) {
                alert('Error saving the post');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endpush
