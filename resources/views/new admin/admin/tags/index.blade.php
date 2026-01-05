@extends('layouts.admin')

@section('title', 'Tags')

@section('content')
<div class="p-6 md:p-8" x-data="tagManager(@js($tags), '{{ $type }}')">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground" data-testid="text-tags-title">
                Tags
            </h1>
            <p class="text-muted-foreground">
                Manage tags for products and posts
            </p>
        </div>
        <button
            @click="openDialog()"
            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
            data-testid="button-create-tag"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Tag
        </button>
    </div>

    <!-- Type Filter -->
    <div class="mb-6 flex gap-2">
        <a href="{{ route('newadmin.tags.index', ['type' => 'all']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $type === 'all' ? 'bg-primary text-primary-foreground' : 'bg-background border border-input text-foreground hover-elevate' }}">
            All Tags
        </a>
        <a href="{{ route('newadmin.tags.index', ['type' => 'product']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $type === 'product' ? 'bg-primary text-primary-foreground' : 'bg-background border border-input text-foreground hover-elevate' }}">
            Product Tags
        </a>
        <a href="{{ route('newadmin.tags.index', ['type' => 'post']) }}" 
           class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ $type === 'post' ? 'bg-primary text-primary-foreground' : 'bg-background border border-input text-foreground hover-elevate' }}">
            Post Tags
        </a>
    </div>

    <!-- Dialog -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-background/80" @click.self="closeDialog()">
        <div class="bg-card border border-card-border rounded-lg p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" @click.stop>
            <h2 class="text-lg font-semibold mb-4" x-text="editingId ? 'Edit Tag' : 'New Tag'"></h2>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Name *</label>
                    <input
                        type="text"
                        x-model="name"
                        @input="if (!editingId) slug = generateSlug(name)"
                        placeholder="Tag name"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-tag-name"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Slug *</label>
                    <input
                        type="text"
                        x-model="slug"
                        placeholder="slug-tag"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono"
                        data-testid="input-tag-slug"
                    />
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Type *</label>
                    <select
                        x-model="tagType"
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    >
                        <option value="product">Product</option>
                        <option value="post">Post</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Description</label>
                    <textarea
                        x-model="description"
                        rows="3"
                        placeholder="Optional description for this tag"
                        class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                    ></textarea>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-medium">Color</label>
                    <div class="flex gap-2">
                        <input
                            type="color"
                            x-model="color"
                            class="h-10 w-20 rounded-md border border-input cursor-pointer"
                        />
                        <input
                            type="text"
                            x-model="color"
                            placeholder="#3b82f6"
                            pattern="^#[0-9A-Fa-f]{6}$"
                            class="flex-1 h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono"
                        />
                    </div>
                    <p class="text-xs text-muted-foreground">Hex color code (e.g., #3b82f6)</p>
                </div>
                <div class="flex items-center gap-2">
                    <input
                        type="checkbox"
                        id="is_active"
                        x-model="isActive"
                        class="w-4 h-4 rounded border-input"
                    />
                    <label for="is_active" class="text-sm font-medium cursor-pointer">Active</label>
                </div>
                <div class="flex gap-2 pt-2">
                    <button
                        @click="saveTag()"
                        :disabled="saving || !name || !slug"
                        class="flex-1 inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                        data-testid="button-save-tag"
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

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($tags->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3 font-semibold">Tag</th>
                            <th class="text-left p-3 font-semibold">Type</th>
                            <th class="text-left p-3 font-semibold">Description</th>
                            <th class="text-left p-3 font-semibold">Status</th>
                            <th class="text-right p-3 font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                        <tr class="border-b hover:bg-muted/50">
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    @if($tag->color)
                                        <span class="w-4 h-4 rounded-full border border-border" style="background-color: {{ $tag->color }}"></span>
                                    @endif
                                    <span class="font-medium">{{ $tag->name }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground font-mono mt-1">{{ $tag->slug }}</p>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $tag->type === 'product' ? 'bg-cyan-500/20 text-cyan-400' : 'bg-violet-500/20 text-violet-400' }}">
                                    {{ ucfirst($tag->type ?? 'product') }}
                                </span>
                            </td>
                            <td class="p-3">
                                <p class="text-sm text-muted-foreground line-clamp-2">{{ $tag->description ?? '—' }}</p>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $tag->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $tag->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        @click="editTag({{ $tag->id }}, '{{ addslashes($tag->name) }}', '{{ $tag->slug }}', '{{ $tag->type ?? 'product' }}', '{{ addslashes($tag->description ?? '') }}', '{{ $tag->color ?? '' }}', {{ $tag->is_active ? 'true' : 'false' }})"
                                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-8 w-8 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                                        data-testid="button-edit-tag-{{ $tag->id }}"
                                    >
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('newadmin.tags.destroy', $tag->id) }}" onsubmit="return confirm('Are you sure you want to delete this tag?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-8 w-8 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                                            data-testid="button-delete-tag-{{ $tag->id }}"
                                        >
                                            <svg class="w-3 h-3 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-muted-foreground mb-4">
                    No tags found
                </p>
                <button @click="openDialog()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create First Tag
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function tagManager(tagsData, currentType) {
    return {
        isOpen: false,
        editingId: null,
        name: '',
        slug: '',
        tagType: currentType === 'all' ? 'product' : currentType,
        description: '',
        color: '#3b82f6',
        isActive: true,
        saving: false,
        
        openDialog() {
            this.isOpen = true;
        },
        
        closeDialog() {
            this.isOpen = false;
            this.editingId = null;
            this.name = '';
            this.slug = '';
            this.tagType = currentType === 'all' ? 'product' : currentType;
            this.description = '';
            this.color = '#3b82f6';
            this.isActive = true;
        },
        
        editTag(id, name, slug, type, description, color, isActive) {
            this.editingId = id;
            this.name = name;
            this.slug = slug;
            this.tagType = type || 'product';
            this.description = description || '';
            this.color = color || '#3b82f6';
            this.isActive = isActive !== undefined ? isActive : true;
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
        
        async saveTag() {
            if (!this.name || !this.slug || !this.tagType) return;
            
            this.saving = true;
            const url = this.editingId 
                ? `/newadmin/tags/${this.editingId}`
                : '/newadmin/tags';
            
            try {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('slug', this.slug);
                formData.append('type', this.tagType);
                formData.append('description', this.description || '');
                if (this.color && this.color.match(/^#[0-9A-Fa-f]{6}$/)) {
                    formData.append('color', this.color);
                }
                if (this.isActive) {
                    formData.append('is_active', '1');
                }
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
                    alert(error.message || 'Error saving tag');
                }
            } catch (error) {
                alert('Error saving the tag');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endpush
@endsection
