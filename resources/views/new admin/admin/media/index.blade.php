@extends('layouts.admin')

@section('title', 'Media')

@section('content')
<div class="p-6 md:p-8" x-data="mediaManager()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-foreground" data-testid="text-media-title">
                Media
            </h1>
            <p class="text-muted-foreground">
                Manage your images and files
            </p>
        </div>
        <div>
            <input
                type="file"
                accept="image/*"
                @change="handleFileUpload($event)"
                class="hidden"
                x-ref="fileInput"
                data-testid="input-file-upload"
            />
            <button
                @click="openFileDialog()"
                :disabled="uploading"
                class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2 disabled:opacity-50"
                data-testid="button-upload"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span x-text="uploading ? 'Uploading...' : 'Upload Image'"></span>
            </button>
        </div>
    </div>

    @if($media->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            @foreach($media as $item)
            <div
                class="group relative aspect-square overflow-hidden rounded-xl border bg-card border-card-border hover-elevate cursor-pointer"
                @click="copyToClipboard('{{ $item->url }}')"
                data-testid="media-item-{{ $item->id }}"
            >
                <img
                    src="{{ $item->url }}"
                    alt="{{ $item->original_name }}"
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <form method="POST" action="{{ route('newadmin.media.destroy', $item->id) }}" onsubmit="return confirm('Are you sure?');" @click.stop>
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 px-3 py-2 bg-destructive text-destructive-foreground border border-destructive-border hover-elevate active-elevate-2"
                            data-testid="button-delete-media-{{ $item->id }}"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs p-2 truncate">
                    {{ $item->original_name }}
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-12 text-center">
            <svg class="w-12 h-12 mx-auto mb-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
            </svg>
            <p class="text-muted-foreground mb-4">
                No images yet
            </p>
            <button @click="openFileDialog()" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Upload First Image
            </button>
        </div>
    @endif
</div>

@push('scripts')
<script>
function mediaManager() {
    return {
        uploading: false,
        
        openFileDialog() {
            const fileInput = this.$refs.fileInput;
            if (fileInput) {
                fileInput.click();
            }
        },
        
        async handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select only image files');
                event.target.value = '';
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                alert('File is too large. Maximum 5MB');
                event.target.value = '';
                return;
            }
            
            this.uploading = true;
            const formData = new FormData();
            formData.append('file', file);
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                const response = await fetch('{{ route('newadmin.media.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: formData,
                });
                
                let responseData;
                try {
                    responseData = await response.json();
                } catch (e) {
                    // If response is not JSON, it might be a redirect or HTML error
                    if (response.ok) {
                        // If status is OK but not JSON, it's probably a redirect - reload
                        window.location.reload();
                        return;
                    }
                    throw new Error('Error processing server response');
                }
                
                if (response.ok && (responseData.success || responseData.media)) {
                    // Success - reload page to show new image
                    window.location.reload();
                } else {
                    // Handle validation errors
                    let errorMessage = 'Error uploading image';
                    if (responseData.message) {
                        errorMessage = responseData.message;
                    } else if (responseData.errors && responseData.errors.file) {
                        errorMessage = Array.isArray(responseData.errors.file) 
                            ? responseData.errors.file[0] 
                            : responseData.errors.file;
                    }
                    alert(errorMessage);
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Error uploading image: ' + (error.message || 'Unknown error'));
            } finally {
                this.uploading = false;
                event.target.value = '';
            }
        },
        
        copyToClipboard(url) {
            navigator.clipboard.writeText(url);
            alert('URL copied to clipboard');
        }
    }
}
</script>
@endpush
@endsection
