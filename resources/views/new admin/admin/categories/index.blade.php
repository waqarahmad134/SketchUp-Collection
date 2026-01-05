@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="p-6 md:p-8" x-data="categoryManager()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground" data-testid="text-categories-title">
                Categories
            </h1>
            <p class="text-muted-foreground">
                Organize your posts in categories
            </p>
        </div>
        <button
            @click="openDialog()"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
            data-testid="button-create-category"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Category
        </button>
    </div>

    <!-- Dialog -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-background/80" @click.self="closeDialog()">
        <div class="bg-card border border-card-border rounded-lg p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-semibold mb-4" x-text="editingId ? 'Edit Category' : 'New Category'"></h2>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Name *</label>
                    <input
                        type="text"
                        x-model="name"
                        @input="if (!editingId) slug = generateSlug(name)"
                        placeholder="Category name"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-category-name"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Slug *</label>
                    <input
                        type="text"
                        x-model="slug"
                        placeholder="slug-categoria"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-category-slug"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Description</label>
                    <textarea
                        x-model="description"
                        placeholder="Category description"
                        rows="3"
                        class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-category-description"
                    ></textarea>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="saveCategory()"
                        :disabled="saving"
                        class="flex-1 inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                        data-testid="button-save-category"
                    >
                        <span x-text="saving ? 'Saving...' : 'Save'"></span>
                    </button>
                    <button
                        @click="closeDialog()"
                        :disabled="saving"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($categories as $category)
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm" data-testid="category-card-{{ $category->id }}">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold leading-none tracking-tight truncate">{{ $category->name }}</h3>
                    <div class="flex items-center gap-2">
                        <button
                            @click="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->slug }}', '{{ addslashes($category->description ?? '') }}')"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                            data-testid="button-edit-category-{{ $category->id }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('newadmin.categories.destroy', $category->id) }}" onsubmit="return confirm('Are you sure?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                                data-testid="button-delete-category-{{ $category->id }}"
                            >
                                <svg class="w-4 h-4 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="p-6 pt-0">
                <p class="text-sm text-muted-foreground mb-2">
                    {{ $category->description ?? 'No description' }}
                </p>
                <code class="text-xs bg-muted px-2 py-1 rounded">
                    {{ $category->slug }}
                </code>
            </div>
        </div>
        @endforeach
    </div>

    @if($categories->count() === 0)
    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-12 text-center">
        <p class="text-muted-foreground mb-4">
            No categories yet
        </p>
        <button @click="openDialog()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create First Category
        </button>
    </div>
    @endif
</div>

@push('scripts')
<script>
function categoryManager() {
    return {
        isOpen: false,
        editingId: null,
        name: '',
        slug: '',
        description: '',
        saving: false,
        
        openDialog() {
            this.isOpen = true;
        },
        
        closeDialog() {
            this.isOpen = false;
            this.editingId = null;
            this.name = '';
            this.slug = '';
            this.description = '';
        },
        
        editCategory(id, name, slug, description) {
            this.editingId = id;
            this.name = name;
            this.slug = slug;
            this.description = description;
            this.isOpen = true;
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
        
        async saveCategory() {
            if (!this.name || !this.slug) return;
            
            this.saving = true;
            const url = this.editingId 
                ? `/newadmin/categories/${this.editingId}`
                : '/newadmin/categories';
            const method = this.editingId ? 'PUT' : 'POST';
            
            try {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('slug', this.slug);
                formData.append('description', this.description);
                if (this.editingId) {
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
                    window.location.reload();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Error saving');
                }
            } catch (error) {
                alert('Error saving the category');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endpush
@endsection
