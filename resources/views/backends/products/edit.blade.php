@extends('backends.master')
@section('contents')
<style>
    .hover-visible {
        transition: opacity 0.3s ease;
    }
    label:hover .hover-visible {
        opacity: 1 !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i> Edit Product: {{ $product->name }}
                    </h5>
                    <div>
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-info btn-sm me-1">
                            <i class="fas fa-eye me-1"></i> View
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" id="product-form">
                        @csrf
                        @method('PUT')

                        <!-- Product Info Section -->
                        <div class="row">
                            <!-- Left Column - Product Details -->
                            <div class="col-md-8">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="brand_id" class="form-label">Brand</label>
                                                <select class="form-select @error('brand_id') is-invalid @enderror" id="brand_id" name="brand_id">
                                                    <option value="">Select Brand</option>
                                                    @foreach ($brands as $brand)
                                                        <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                            {{ $brand->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('brand_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                                @error('status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pricing & Inventory</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="price" class="form-label">Regular Price <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="discount_price" class="form-label">Sale Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                                    @error('discount_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="cost_price" class="form-label">Cost Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}">
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                                @error('stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-8">
                                                <label for="is_featured" class="form-label d-block">Featured Status</label>
                                                <div class="card border p-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="is_featured">Feature this product on homepage</label>
                                                    </div>
                                                    <small class="text-muted">Featured products will be displayed prominently on the homepage</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Product Description</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="short_description" class="form-label">Short Description</label>
                                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                                            @error('short_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="description" class="form-label">Full Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - SEO & Upload -->
                            <div class="col-md-4">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">SEO Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="meta_title" class="form-label">Meta Title</label>
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}">
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="2">{{ old('meta_description', $product->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Add More Images</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="image-upload-container mb-3">
                                            <div class="dropzone-container p-3 text-center border rounded">
                                                <div class="mb-3">
                                                    <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                                </div>
                                                <p class="small mb-2">Drag and drop images here or click to browse</p>
                                                <input type="file" id="product-images" name="product_images[]" class="form-control @error('product_images') is-invalid @enderror" multiple accept="image/*" onchange="previewImages(this)">
                                                @error('product_images')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <div id="image-preview-container" class="mt-3 d-flex flex-wrap gap-2">
                                                    <!-- Image previews will be shown here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="form-text text-muted">
                                                <ul class="ps-3 mb-0">
                                                    <li>Recommended size: 800x800 pixels</li>
                                                    <li>Maximum file size: 2MB per image</li>
                                                    <li>Supported formats: JPG, JPEG, PNG</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> Update Product
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>

                        <!-- Current Images Section - Full Width -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Current Images</h6>
                                        <span class="badge bg-primary">{{ $product->productImages->count() }} images</span>
                                    </div>
                                    <div class="card-body">
                                        @if($product->productImages->count() > 0)
                                            <div class="row g-4">
                                                @foreach($product->productImages as $image)
                                                    <div class="col-md-3" id="image-wrapper-{{ $image->id }}">
                                                        <div class="card shadow-sm h-80 {{ $image->is_primary ? 'border border-primary border-2' : '' }}">
                                                            <!-- Image with click handler -->
                                                            <div class="position-relative overflow-hidden">
                                                                <label for="primary_image_{{ $image->id }}" class="mb-0 d-block" style="cursor: pointer;">
                                                                    <img src="{{ asset($image->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 180px; object-fit: cover;">
                                                                    @if($image->is_primary)
                                                                        <div class="position-absolute top-0 start-0 w-100 bg-primary text-white text-center py-1">
                                                                            <small><i class="fas fa-star me-1"></i> Primary Image</small>
                                                                        </div>
                                                                    @else
                                                                        <div class="position-absolute top-0 start-0 w-100 bg-secondary bg-opacity-50 text-white text-center py-1 opacity-0 hover-visible">
                                                                            <small>Click to set as primary</small>
                                                                        </div>
                                                                    @endif
                                                                </label>
                                                                <!-- Hidden radio button -->
                                                                <input class="primary-image-radio visually-hidden" type="radio" name="primary_image" id="primary_image_{{ $image->id }}" value="{{ $image->id }}" {{ $image->is_primary ? 'checked' : '' }}>
                                                            </div>
                                                            <!-- Delete button in card footer -->
                                                            <div class="card-footer bg-white border-top p-2 text-center">
                                                                <button type="button" class="btn btn-danger btn-sm w-100 delete-image-btn" data-image-id="{{ $image->id }}">
                                                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-warning mb-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i> No images found for this product. Please upload at least one image.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms for image operations -->
<form id="delete-image-form" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<form id="set-primary-form" action="" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@push('scripts')
<script>
    // Image preview functionality for newly added images
    function previewImages(input) {
        const previewContainer = document.getElementById('image-preview-container');
        previewContainer.innerHTML = '';

        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const reader = new FileReader();
                const file = input.files[i];

                reader.onload = function(e) {
                    const previewWrapper = document.createElement('div');
                    previewWrapper.className = 'position-relative';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';

                    const badge = document.createElement('span');
                    if (i === 0 && document.querySelectorAll('.primary-image-radio:checked').length === 0) {
                        badge.className = 'position-absolute top-0 start-0 badge bg-primary';
                        badge.innerHTML = 'New Main';
                    }

                    previewWrapper.appendChild(img);
                    previewWrapper.appendChild(badge);
                    previewContainer.appendChild(previewWrapper);
                };

                reader.readAsDataURL(file);
            }
        }
    }

    // Set up event handlers
    document.addEventListener('DOMContentLoaded', function() {
        // Handle setting primary image when radio is clicked
        const primaryRadios = document.querySelectorAll('.primary-image-radio');
        primaryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    // Show loading state
                    const imageWrapper = this.closest('#image-wrapper-' + this.value);
                    if (imageWrapper) {
                        imageWrapper.style.opacity = '0.7';
                    }
                    setPrimaryImage(this.value);
                }
            });
        });

        // Handle deleting images
        const deleteButtons = document.querySelectorAll('.delete-image-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const imageId = this.getAttribute('data-image-id');
                const imageWrapper = document.querySelector('#image-wrapper-' + imageId);

                if (confirm('Are you sure you want to delete this image?')) {
                    // Show loading state
                    if (imageWrapper) {
                        imageWrapper.style.opacity = '0.5';
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
                        this.disabled = true;
                    }
                    deleteImage(imageId);
                }
            });
        });
    });

    // Function to set primary image
    function setPrimaryImage(imageId) {
        const form = document.getElementById('set-primary-form');
        form.action = "{{ route('products.setPrimaryImage', ['productId' => $product->id, 'imageId' => 0]) }}".replace('/0', '/' + imageId);
        form.submit();
    }

    // Function to delete image
    function deleteImage(imageId) {
        console.log('Deleting image: ' + imageId); // Debug log
        const form = document.getElementById('delete-image-form');
        form.action = "{{ route('products.deleteImage', ['imageId' => '__ID__']) }}".replace('__ID__', imageId);
        form.submit();
    }
</script>
@endpush
