@php
$product = $product ?? null;
$isEditMode = $isEditMode ?? false;
$fullDescription = ($isEditMode && $product) ? ($product->full_description ?? '') : '';
$initialFeatures = old('features', ($isEditMode && $product && $product->features) ? $product->features : []);
$initialDownloadLinks = old('download_links', ($isEditMode && $product && $product->download_links) ? $product->download_links : []);
$initialIncludedProducts = old('included_products', ($isEditMode && $product && $product->included_products) ? $product->included_products : []);
$initialTags = old('tags', ($isEditMode && $product && $product->tags) ? $product->tags->pluck('id')->toArray() : []);
@endphp

<div class="p-6 md:p-8" x-data="productEditor({{ $isEditMode ? 'true' : 'false' }}, @js($fullDescription), @js($categories->toArray()), @js($tags->toArray()), @js($allProducts->pluck('title', 'id')->toArray()), @js($initialFeatures), @js($initialDownloadLinks), @js($initialIncludedProducts), @js($initialTags))" x-init="init()">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.products.index') }}">
            <button type="button" class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-9 w-9 border border-input bg-background text-foreground hover-elevate active-elevate-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </button>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-foreground">
                {{ $isEditMode ? 'Edit Product' : 'New Product' }}
            </h1>
            <p class="text-muted-foreground">
                {{ $isEditMode ? 'Update product information' : 'Create a new product' }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ $isEditMode && $product ? route('admin.products.update', $product->id) : route('admin.products.store') }}" enctype="multipart/form-data" @submit="syncFormData()">
        @csrf
        @if($isEditMode)
            @method('PUT')
        @endif
        
        <div class="space-y-8">
            <!-- Basic Information -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Basic Information</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="title" class="block text-sm font-medium mb-2">Title *</label>
                            <input
                                type="text"
                                id="title"
                                name="title"
                                x-model="title"
                                @input="if (!isEditMode) slug = generateSlug(title)"
                                value="{{ old('title', $isEditMode && $product ? $product->title : '') }}"
                                required
                                maxlength="255"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                            @error('title')
                                <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium mb-2">Slug *</label>
                            <input
                                type="text"
                                id="slug"
                                name="slug"
                                x-model="slug"
                                value="{{ old('slug', $isEditMode && $product ? $product->slug : '') }}"
                                required
                                maxlength="255"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono"
                            >
                            @error('slug')
                                <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium mb-2">Description *</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            required
                            maxlength="500"
                            class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >{{ old('description', $isEditMode && $product ? $product->description : '') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="full_description" class="block text-sm font-medium mb-2">Full Description</label>
                        <div x-ref="editorContainer" class="bg-background rounded-md border border-input">
                            <div id="full-description-editor" style="min-height: 300px;"></div>
                        </div>
                        <textarea
                            x-model="fullDescription"
                            id="full_description_hidden"
                            name="full_description"
                            style="display: none;"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Pricing</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-sm font-medium mb-2">Price *</label>
                        <input
                            type="number"
                            id="price"
                            name="price"
                            step="0.01"
                            min="0"
                            value="{{ old('price', $isEditMode && $product ? $product->price : 0) }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        @error('price')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="original_price" class="block text-sm font-medium mb-2">Original Price *</label>
                        <input
                            type="number"
                            id="original_price"
                            name="original_price"
                            step="0.01"
                            min="0"
                            value="{{ old('original_price', $isEditMode && $product ? $product->original_price : 0) }}"
                            required
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        @error('original_price')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Product Details -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Product Details</h2>
                <div class="space-y-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium mb-2">Category</label>
                        <select
                            id="category_id"
                            name="category_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                            <option value="">Select category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $isEditMode && $product ? $product->category_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="is_bundle"
                                name="is_bundle"
                                value="1"
                                x-model="isBundle"
                                {{ old('is_bundle', $isEditMode && $product && $product->is_bundle ? 'checked' : '') ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-input"
                            >
                            <label for="is_bundle" class="text-sm font-medium">Is Bundle</label>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="is_digital"
                                name="is_digital"
                                value="1"
                                x-model="isDigital"
                                {{ old('is_digital', $isEditMode && $product ? ($product->is_digital ?? true) : true) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-input"
                            >
                            <label for="is_digital" class="text-sm font-medium">Is Digital</label>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                type="checkbox"
                                id="is_active"
                                name="is_active"
                                value="1"
                                {{ old('is_active', $isEditMode && $product ? ($product->is_active ?? true) : true) ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-input"
                            >
                            <label for="is_active" class="text-sm font-medium">Active</label>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label for="file_count" class="block text-sm font-medium mb-2">File Count</label>
                            <input
                                type="number"
                                id="file_count"
                                name="file_count"
                                min="0"
                                value="{{ old('file_count', $isEditMode && $product ? ($product->file_count ?? 0) : 0) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                        </div>

                        <div>
                            <label for="file_size" class="block text-sm font-medium mb-2">File Size</label>
                            <input
                                type="text"
                                id="file_size"
                                name="file_size"
                                value="{{ old('file_size', $isEditMode && $product ? ($product->file_size ?? '') : '') }}"
                                maxlength="255"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="e.g., 500 MB"
                            >
                        </div>

                        <div>
                            <label for="sketchup_version" class="block text-sm font-medium mb-2">SketchUp Version</label>
                            <input
                                type="text"
                                id="sketchup_version"
                                name="sketchup_version"
                                value="{{ old('sketchup_version', $isEditMode && $product ? ($product->sketchup_version ?? '') : '') }}"
                                maxlength="255"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="e.g., 2021+"
                            >
                        </div>

                        <div>
                            <label for="sort_order" class="block text-sm font-medium mb-2">Sort Order</label>
                            <input
                                type="number"
                                id="sort_order"
                                name="sort_order"
                                value="{{ old('sort_order', $isEditMode && $product ? ($product->sort_order ?? 0) : 0) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Media</h2>
                <div class="space-y-4">
                    <div>
                        <label for="image" class="block text-sm font-medium mb-2">Main Image {{ !$isEditMode ? '*' : '' }}</label>
                        @if($isEditMode && $product && $product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="w-32 h-32 rounded object-cover">
                            </div>
                        @endif
                        <input
                            type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                            {{ !$isEditMode ? 'required' : '' }}
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        @error('image')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="image_alt" class="block text-sm font-medium mb-2">Image Alt Text <span class="text-xs text-muted-foreground font-normal">(SEO)</span></label>
                        <input
                            type="text"
                            id="image_alt"
                            name="image_alt"
                            value="{{ old('image_alt', $isEditMode && $product ? ($product->image_alt ?? '') : '') }}"
                            maxlength="255"
                            placeholder="e.g., Modern living room SketchUp 3D model bundle"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">Describe the image for search engines. Falls back to the product title.</p>
                        @error('image_alt')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="images" class="block text-sm font-medium mb-2">Gallery Images</label>
                        @if($isEditMode && $product && isset($product->images) && is_array($product->images) && count($product->images) > 0)
                            <div class="grid grid-cols-4 gap-2 mb-2">
                                @foreach($product->images as $img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="Gallery image" class="w-20 h-20 rounded object-cover">
                                @endforeach
                            </div>
                        @endif
                        <input
                            type="file"
                            id="images"
                            name="images[]"
                            accept="image/*"
                            multiple
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">You can select multiple images</p>
                    </div>

                    <div x-show="isDigital">
                        <label for="download_file" class="block text-sm font-medium mb-2">Download File</label>
                        @if($isEditMode && $product && $product->download_file)
                            <p class="text-sm text-muted-foreground mb-2">Current file: {{ basename($product->download_file) }}</p>
                        @endif
                        <input
                            type="file"
                            id="download_file"
                            name="download_file"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">For digital products</p>
                    </div>

                    <div x-show="isDigital">
                        <label for="sample_file" class="block text-sm font-medium mb-2">Free Sample File</label>
                        @if($isEditMode && $product && $product->sample_file)
                            <p class="text-sm text-muted-foreground mb-2">Current sample: {{ basename($product->sample_file) }}</p>
                        @endif
                        <input
                            type="file"
                            id="sample_file"
                            name="sample_file"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <p class="mt-1 text-xs text-muted-foreground">A free preview file buyers can download before purchasing</p>
                        @error('sample_file')
                            <p class="mt-1 text-sm text-destructive">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Download Links Repeater -->
                    <div x-show="isDigital">
                        <label class="block text-sm font-medium mb-2">Download Links</label>
                        <div class="space-y-2">
                            <template x-for="(link, index) in downloadLinks" :key="index">
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        x-model="link.title"
                                        :name="`download_links[${index}][title]`"
                                        placeholder="Link title (e.g., Mega Drive)"
                                        class="flex-1 h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                    <input
                                        type="url"
                                        x-model="link.url"
                                        :name="`download_links[${index}][url]`"
                                        placeholder="https://..."
                                        class="flex-1 h-10 rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                    <button type="button" @click="downloadLinks.splice(index, 1)" class="h-10 px-3 border border-input rounded-md hover-elevate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="downloadLinks.push({title: '', url: ''})" class="text-sm text-primary hover:underline">
                                + Add Link
                            </button>
                        </div>
                        <input type="hidden" name="download_links" :value="JSON.stringify(downloadLinks)">
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <h2 class="text-xl font-semibold mb-4">Additional Information</h2>
                <div class="space-y-4">
                    <div>
                        <label for="features" class="block text-sm font-medium mb-2">Features</label>
                        <input
                            type="text"
                            id="features_input"
                            x-model="featuresInput"
                            @keydown.enter.prevent="addFeature()"
                            placeholder="Add a feature and press Enter"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        <input type="hidden" name="features" :value="JSON.stringify(features)">
                        <div class="flex flex-wrap gap-2 mt-2">
                            <template x-for="(feature, index) in features" :key="index">
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-muted rounded text-sm">
                                    <span x-text="feature"></span>
                                    <button type="button" @click="features.splice(index, 1)" class="hover:text-destructive">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label for="tags" class="block text-sm font-medium mb-2">Tags</label>
                        <div class="border border-input rounded-md bg-background p-3 min-h-32 max-h-48 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($tags as $tag)
                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-muted/50 p-2 rounded">
                                        <input
                                            type="checkbox"
                                            :value="{{ $tag->id }}"
                                            x-model="selectedTags"
                                            class="w-4 h-4 rounded border-input text-primary focus:ring-2 focus:ring-ring"
                                        >
                                        <span class="text-sm text-foreground">{{ $tag->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <template x-for="tagId in selectedTags" :key="tagId">
                            <input type="hidden" name="tags[]" :value="tagId">
                        </template>
                        <p class="mt-1 text-xs text-muted-foreground">
                            <span x-text="selectedTags.length"></span> tag(s) selected
                        </p>
                    </div>

                    <div x-show="isBundle">
                        <label for="included_products" class="block text-sm font-medium mb-2">Included Products</label>
                        <div class="border border-input rounded-md bg-background p-3 min-h-32 max-h-48 overflow-y-auto">
                            <div class="space-y-2">
                                @foreach($allProducts as $prod)
                                    @if(!$isEditMode || ($product && $prod->id != $product->id))
                                        <label class="flex items-center gap-2 cursor-pointer hover:bg-muted/50 p-2 rounded">
                                            <input
                                                type="checkbox"
                                                :value="{{ $prod->id }}"
                                                x-model="includedProducts"
                                                class="w-4 h-4 rounded border-input text-primary focus:ring-2 focus:ring-ring"
                                            >
                                            <span class="text-sm text-foreground">{{ $prod->title }}</span>
                                        </label>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="included_products" :value="JSON.stringify(includedProducts)">
                        <p class="mt-1 text-xs text-muted-foreground">
                            <span x-text="includedProducts.length"></span> product(s) selected
                        </p>
                    </div>
                </div>
            </div>

            <!-- SEO & Meta Tags -->
            <div class="shadcn-card rounded-xl border bg-card border-card-border text-card-foreground shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold">SEO & Meta Tags</h2>
                    <button type="button" @click="seoExpanded = !seoExpanded" class="text-sm text-muted-foreground hover:text-foreground">
                        <span x-text="seoExpanded ? 'Collapse' : 'Expand'"></span>
                    </button>
                </div>
                
                <div x-show="seoExpanded" class="space-y-6">
                    <!-- Basic SEO -->
                    <div>
                        <h3 class="text-lg font-medium mb-3">Basic SEO</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="meta_title" class="block text-sm font-medium mb-2">Meta Title</label>
                                <input
                                    type="text"
                                    id="meta_title"
                                    name="meta_title"
                                    value="{{ old('meta_title', $isEditMode && $product ? ($product->meta_title ?? '') : '') }}"
                                    maxlength="60"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use product title</p>
                            </div>

                            <div>
                                <label for="meta_description" class="block text-sm font-medium mb-2">Meta Description</label>
                                <textarea
                                    id="meta_description"
                                    name="meta_description"
                                    rows="3"
                                    maxlength="155"
                                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >{{ old('meta_description', $isEditMode && $product ? ($product->meta_description ?? '') : '') }}</textarea>
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use product description</p>
                            </div>

                            <div>
                                <label for="canonical_url" class="block text-sm font-medium mb-2">Canonical URL</label>
                                <input
                                    type="url"
                                    id="canonical_url"
                                    name="canonical_url"
                                    value="{{ old('canonical_url', $isEditMode && $product ? ($product->canonical_url ?? '') : '') }}"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use current URL</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="robots_index" class="block text-sm font-medium mb-2">Robots Index</label>
                                    <select
                                        id="robots_index"
                                        name="robots_index"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                        <option value="index" {{ old('robots_index', $isEditMode && $product ? ($product->robots_index ?? 'index') : 'index') === 'index' ? 'selected' : '' }}>Index</option>
                                        <option value="noindex" {{ old('robots_index', $isEditMode && $product ? ($product->robots_index ?? 'index') : 'index') === 'noindex' ? 'selected' : '' }}>No Index</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="robots_follow" class="block text-sm font-medium mb-2">Robots Follow</label>
                                    <select
                                        id="robots_follow"
                                        name="robots_follow"
                                        class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                    >
                                        <option value="follow" {{ old('robots_follow', $isEditMode && $product ? ($product->robots_follow ?? 'follow') : 'follow') === 'follow' ? 'selected' : '' }}>Follow</option>
                                        <option value="nofollow" {{ old('robots_follow', $isEditMode && $product ? ($product->robots_follow ?? 'follow') : 'follow') === 'nofollow' ? 'selected' : '' }}>No Follow</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Open Graph -->
                    <div class="border-t pt-4">
                        <h3 class="text-lg font-medium mb-3">Open Graph</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="og_title" class="block text-sm font-medium mb-2">OG Title</label>
                                <input
                                    type="text"
                                    id="og_title"
                                    name="og_title"
                                    value="{{ old('og_title', $isEditMode && $product ? ($product->og_title ?? '') : '') }}"
                                    maxlength="60"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use meta title</p>
                            </div>

                            <div>
                                <label for="og_description" class="block text-sm font-medium mb-2">OG Description</label>
                                <textarea
                                    id="og_description"
                                    name="og_description"
                                    rows="3"
                                    maxlength="200"
                                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >{{ old('og_description', $isEditMode && $product ? ($product->og_description ?? '') : '') }}</textarea>
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use meta description</p>
                            </div>

                            <div>
                                <label for="og_image" class="block text-sm font-medium mb-2">OG Image</label>
                                @if($isEditMode && $product && $product->og_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $product->og_image) }}" alt="OG Image" class="w-32 h-32 rounded object-cover">
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    id="og_image"
                                    name="og_image"
                                    accept="image/*"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use product image</p>
                            </div>

                            <div>
                                <label for="og_type" class="block text-sm font-medium mb-2">OG Type</label>
                                <select
                                    id="og_type"
                                    name="og_type"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    <option value="product" {{ old('og_type', $isEditMode && $product ? ($product->og_type ?? 'product') : 'product') === 'product' ? 'selected' : '' }}>Product</option>
                                    <option value="website" {{ old('og_type', $isEditMode && $product ? ($product->og_type ?? 'product') : 'product') === 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="article" {{ old('og_type', $isEditMode && $product ? ($product->og_type ?? 'product') : 'product') === 'article' ? 'selected' : '' }}>Article</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Twitter Card -->
                    <div class="border-t pt-4">
                        <h3 class="text-lg font-medium mb-3">Twitter Card</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="twitter_card" class="block text-sm font-medium mb-2">Twitter Card Type</label>
                                <select
                                    id="twitter_card"
                                    name="twitter_card"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                    <option value="summary" {{ old('twitter_card', $isEditMode && $product ? ($product->twitter_card ?? 'summary_large_image') : 'summary_large_image') === 'summary' ? 'selected' : '' }}>Summary</option>
                                    <option value="summary_large_image" {{ old('twitter_card', $isEditMode && $product ? ($product->twitter_card ?? 'summary_large_image') : 'summary_large_image') === 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                                    <option value="player" {{ old('twitter_card', $isEditMode && $product ? ($product->twitter_card ?? 'summary_large_image') : 'summary_large_image') === 'player' ? 'selected' : '' }}>Player</option>
                                    <option value="app" {{ old('twitter_card', $isEditMode && $product ? ($product->twitter_card ?? 'summary_large_image') : 'summary_large_image') === 'app' ? 'selected' : '' }}>App</option>
                                </select>
                            </div>

                            <div>
                                <label for="twitter_title" class="block text-sm font-medium mb-2">Twitter Title</label>
                                <input
                                    type="text"
                                    id="twitter_title"
                                    name="twitter_title"
                                    value="{{ old('twitter_title', $isEditMode && $product ? ($product->twitter_title ?? '') : '') }}"
                                    maxlength="70"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use meta title</p>
                            </div>

                            <div>
                                <label for="twitter_description" class="block text-sm font-medium mb-2">Twitter Description</label>
                                <textarea
                                    id="twitter_description"
                                    name="twitter_description"
                                    rows="3"
                                    maxlength="200"
                                    class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >{{ old('twitter_description', $isEditMode && $product ? ($product->twitter_description ?? '') : '') }}</textarea>
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use meta description</p>
                            </div>

                            <div>
                                <label for="twitter_image" class="block text-sm font-medium mb-2">Twitter Image</label>
                                @if($isEditMode && $product && $product->twitter_image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $product->twitter_image) }}" alt="Twitter Image" class="w-32 h-32 rounded object-cover">
                                    </div>
                                @endif
                                <input
                                    type="file"
                                    id="twitter_image"
                                    name="twitter_image"
                                    accept="image/*"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                                >
                                <p class="mt-1 text-xs text-muted-foreground">Leave blank to use product image</p>
                            </div>
                        </div>
                    </div>

                    <!-- Schema.org -->
                    <div class="border-t pt-4">
                        <h3 class="text-lg font-medium mb-3">Schema.org</h3>
                        <div>
                            <label for="schema_markup" class="block text-sm font-medium mb-2">Custom Schema JSON-LD</label>
                            <textarea
                                id="schema_markup"
                                name="schema_markup"
                                rows="10"
                                class="flex w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring font-mono"
                                {{-- Avoid Blade interpreting @context --}}
                                placeholder='{"context": "https://schema.org", "type": "Product", "...": "..."}'
                            >{{ old('schema_markup', $isEditMode && $product && $product->schema_markup ? (is_array($product->schema_markup) ? json_encode($product->schema_markup, JSON_PRETTY_PRINT) : $product->schema_markup) : '') }}</textarea>
                            <p class="mt-1 text-xs text-muted-foreground">Custom JSON-LD schema. Leave blank to use auto-generated schema.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 bg-primary text-primary-foreground border border-primary-border hover-elevate active-elevate-2"
                >
                    {{ $isEditMode ? 'Update Product' : 'Create Product' }}
                </button>
                <a href="{{ route('admin.products.index') }}">
                    <button
                        type="button"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium min-h-10 px-4 py-2 border border-input bg-background text-foreground hover-elevate active-elevate-2"
                    >
                        Cancel
                    </button>
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function productEditor(isEdit, fullDescriptionContent, categoriesData, tagsData, allProductsData, initialFeatures, initialDownloadLinks, initialIncludedProducts, initialTags) {
    return {
        isEditMode: isEdit,
        title: '',
        slug: '',
        fullDescription: fullDescriptionContent || '',
        isBundle: false,
        isDigital: true,
        seoExpanded: false,
        features: initialFeatures || [],
        featuresInput: '',
        downloadLinks: initialDownloadLinks || [],
        includedProducts: Array.isArray(initialIncludedProducts) ? initialIncludedProducts : [],
        selectedTags: Array.isArray(initialTags) ? initialTags : [],
        quill: null,
        
        init() {
            this.$nextTick(() => {
                this.initEditor();
            });
        },
        
        initEditor() {
            if (this.quill || this.editorInitialized) {
                return;
            }
            
            const self = this;
            const editorContainer = document.getElementById('full-description-editor');
            
            if (!editorContainer) {
                console.error('Editor container not found');
                return;
            }
            
            if (document.querySelector('#full-description-editor + .ql-toolbar')) {
                this.editorInitialized = true;
                return;
            }
            
            this.editorInitialized = true;
            editorContainer.innerHTML = '';
            
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
                placeholder: 'Enter detailed product description...',
            });
            
            if (this.fullDescription) {
                this.quill.root.innerHTML = this.fullDescription;
            }
            
            this.quill.on('text-change', function() {
                const html = self.quill.root.innerHTML;
                self.fullDescription = html;
                const hiddenTextarea = document.getElementById('full_description_hidden');
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
        
        addFeature() {
            if (this.featuresInput.trim()) {
                this.features.push(this.featuresInput.trim());
                this.featuresInput = '';
            }
        },
        
        syncFormData() {
            // Sync Quill editor content before form submission
            if (this.quill) {
                this.fullDescription = this.quill.root.innerHTML;
                const hiddenTextarea = document.getElementById('full_description_hidden');
                if (hiddenTextarea) {
                    hiddenTextarea.value = this.fullDescription;
                }
            }
            
            // Sync features array to JSON string
            const featuresInput = document.querySelector('input[name="features"]');
            if (featuresInput) {
                featuresInput.value = JSON.stringify(this.features);
            }
            
            // Sync download links array to JSON string
            const downloadLinksInput = document.querySelector('input[name="download_links"]');
            if (downloadLinksInput) {
                downloadLinksInput.value = JSON.stringify(this.downloadLinks);
            }

            // Sync included products array to JSON string
            const includedProductsInput = document.querySelector('input[name="included_products"]');
            if (includedProductsInput) {
                includedProductsInput.value = JSON.stringify(this.includedProducts);
            }
        }
    };
}
</script>
@endpush
