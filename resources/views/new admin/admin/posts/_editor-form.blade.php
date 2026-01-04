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
        'read_time' => $post->read_time,
        'tags' => $post->tags->map(fn($tag) => ['id' => $tag->id])->toArray(),
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
                {{ $isEditMode ? 'Editar Post' : 'Nuevo Post' }}
            </h1>
            <p class="text-muted-foreground">
                {{ $isEditMode ? 'Modifica el contenido de tu post' : 'Crea un nuevo post para tu blog' }}
            </p>
        </div>
    </div>

    <form @submit.prevent="savePost()" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Editor -->
        <div class="lg:col-span-2 space-y-6">
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Título *</label>
                        <input
                            type="text"
                            x-model="title"
                            @input="if (!isEditMode) slug = generateSlug(title)"
                            placeholder="Título del post"
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
                        <label class="text-sm font-medium">Extracto</label>
                        <textarea
                            x-model="excerpt"
                            placeholder="Breve descripción del post"
                            rows="3"
                            class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="input-excerpt"
                        ></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Contenido *</label>
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
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Publicación</h3>
                </div>
                <div class="p-6 pt-0 space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm font-medium">Estado</label>
                        <select
                            x-model="status"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="select-status"
                        >
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Categoría</label>
                        <select
                            x-model="categoryId"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="select-category"
                        >
                            <option value="">Selecciona una categoría</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium">Tiempo de lectura</label>
                        <input
                            type="text"
                            x-model="readTime"
                            placeholder="5 min"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            data-testid="input-read-time"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="saving"
                        class="w-full inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                        data-testid="button-save"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span x-text="saving ? 'Guardando...' : 'Guardar Post'"></span>
                    </button>
                </div>
            </div>

            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-lg font-semibold leading-none tracking-tight">Imagen Destacada</h3>
                </div>
                <div class="p-6 pt-0">
                    <input
                        type="text"
                        x-model="featuredImage"
                        placeholder="URL de la imagen"
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
        readTime: postData?.read_time || '',
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
                alert('Por favor completa todos los campos requeridos');
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
                formData.append('read_time', this.readTime);
                formData.append('tagIds', JSON.stringify(this.selectedTags));
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
                    alert(error.message || 'Error al guardar el post');
                }
            } catch (error) {
                alert('Error al guardar el post');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endpush
