@extends('layouts.admin')

@section('title', 'Tags')

@section('content')
<div class="p-6 md:p-8" x-data="tagManager()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground" data-testid="text-tags-title">
                Tags
            </h1>
            <p class="text-muted-foreground">
                Etiqueta tus posts para mejor organización
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
            Nuevo Tag
        </button>
    </div>

    <!-- Dialog -->
    <div x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-background/80" @click.self="closeDialog()">
        <div class="bg-card border border-card-border rounded-lg p-6 w-full max-w-md mx-4" @click.stop>
            <h2 class="text-lg font-semibold mb-4" x-text="editingId ? 'Editar Tag' : 'Nuevo Tag'"></h2>
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="text-sm font-medium">Nombre *</label>
                    <input
                        type="text"
                        x-model="name"
                        @input="if (!editingId) slug = generateSlug(name)"
                        placeholder="Nombre del tag"
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
                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        data-testid="input-tag-slug"
                    />
                </div>
                <div class="flex gap-2">
                    <button
                        @click="saveTag()"
                        :disabled="saving"
                        class="flex-1 inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                        data-testid="button-save-tag"
                    >
                        <span x-text="saving ? 'Guardando...' : 'Guardar'"></span>
                    </button>
                    <button
                        @click="closeDialog()"
                        :disabled="saving"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                    >
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
        @if($tags->count() > 0)
            <div class="flex flex-wrap gap-3">
                @foreach($tags as $tag)
                <div
                    class="flex items-center gap-2 p-2 rounded-lg border hover-elevate"
                    data-testid="tag-item-{{ $tag->id }}"
                >
                    <span class="px-2 py-1 rounded text-sm bg-secondary text-secondary-foreground">{{ $tag->name }}</span>
                    <button
                        @click="editTag({{ $tag->id }}, '{{ addslashes($tag->name) }}', '{{ $tag->slug }}')"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-8 w-8 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                        data-testid="button-edit-tag-{{ $tag->id }}"
                    >
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <form method="POST" action="{{ route('admin.tags.destroy', $tag->id) }}" onsubmit="return confirm('¿Estás seguro?');" class="inline">
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
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-muted-foreground mb-4">
                    No hay tags aún
                </p>
                <button @click="openDialog()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Primer Tag
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function tagManager() {
    return {
        isOpen: false,
        editingId: null,
        name: '',
        slug: '',
        saving: false,
        
        openDialog() {
            this.isOpen = true;
        },
        
        closeDialog() {
            this.isOpen = false;
            this.editingId = null;
            this.name = '';
            this.slug = '';
        },
        
        editTag(id, name, slug) {
            this.editingId = id;
            this.name = name;
            this.slug = slug;
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
            if (!this.name || !this.slug) return;
            
            this.saving = true;
            const url = this.editingId 
                ? `/admin/tags/${this.editingId}`
                : '/admin/tags';
            
            try {
                const formData = new FormData();
                formData.append('name', this.name);
                formData.append('slug', this.slug);
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
                    alert(error.message || 'Error al guardar');
                }
            } catch (error) {
                alert('Error al guardar el tag');
            } finally {
                this.saving = false;
            }
        }
    }
}
</script>
@endpush
@endsection
