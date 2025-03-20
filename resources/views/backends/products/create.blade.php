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
                        <i class="fas fa-plus-circle me-2"></i> Create New Product
                    </h5>
                    <a href="{{ route('products.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Back to List
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" id="product-form">
                        @csrf

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
                                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
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
                                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                                    <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required>
                                                    @error('price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="discount_price" class="form-label">Sale Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price') }}">
                                                    @error('discount_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="cost_price" class="form-label">Cost Price</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" step="0.01" min="0" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price') }}">
                                                    @error('cost_price')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                                <input type="number" min="0" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required>
                                                @error('stock')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-8">
                                                <label for="is_featured" class="form-label d-block">Featured Status</label>
                                                <div class="card border p-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
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
                                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
                                            @error('short_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="description" class="form-label">Full Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description') }}</textarea>
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
                                            <input type="text" class="form-control @error('meta_title') is-invalid @enderror" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                            <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-0">
                                            <label for="meta_description" class="form-label">Meta Description</label>
                                            <textarea class="form-control @error('meta_description') is-invalid @enderror" id="meta_description" name="meta_description" rows="2">{{ old('meta_description') }}</textarea>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="card shadow-sm mb-4">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Product Images</h6>
                                        <span class="badge bg-danger">Required</span>
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
                                                <div id="image-preview-container" class="mt-3 row g-2">
                                                    <!-- Image previews will be shown here -->
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-text text-muted">
                                                <ul class="ps-3 mb-0">
                                                    <li>Recommended size: 800x800 pixels</li>
                                                    <li>Maximum file size: 2MB per image</li>
                                                    <li>Supported formats: JPG, JPEG, PNG</li>
                                                    <li>First uploaded image will be set as primary image</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch mb-0 mt-3">
                                            <input class="form-check-input" type="checkbox" id="compress_images" name="compress_images" value="1" checked>
                                            <label class="form-check-label" for="compress_images">Optimize images for web</label>
                                            <div class="form-text small">Reduces file size while maintaining quality</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> Create Product
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Image preview functionality
    function previewImages(input) {
        const previewContainer = document.getElementById('image-preview-container');
        previewContainer.innerHTML = '';

        if (input.files && input.files.length > 0) {
            for (let i = 0; i < input.files.length; i++) {
                const reader = new FileReader();
                const file = input.files[i];

                reader.onload = function(e) {
                    const previewCol = document.createElement('div');
                    previewCol.className = 'col-6';

                    const previewCard = document.createElement('div');
                    previewCard.className = 'card h-100 ' + (i === 0 ? 'border border-primary border-2' : '');

                    const imageContainer = document.createElement('div');
                    imageContainer.className = 'position-relative overflow-hidden';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'card-img-top';
                    img.style.height = '120px';
                    img.style.objectFit = 'cover';

                    imageContainer.appendChild(img);

                    if (i === 0) {
                        const primaryBadge = document.createElement('div');
                        primaryBadge.className = 'position-absolute top-0 start-0 w-100 bg-primary text-white text-center py-1';

                        const badgeText = document.createElement('small');
                        badgeText.innerHTML = '<i class="fas fa-star me-1"></i> Will be primary';

                        primaryBadge.appendChild(badgeText);
                        imageContainer.appendChild(primaryBadge);
                    }

                    previewCard.appendChild(imageContainer);
                    previewCol.appendChild(previewCard);
                    previewContainer.appendChild(previewCol);
                };

                reader.readAsDataURL(file);
            }
        }
    }

    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('product-form');

        form.addEventListener('submit', function(event) {
            const productImages = document.getElementById('product-images');
            if (productImages.files.length === 0) {
                alert('Please upload at least one product image');
                event.preventDefault();
                productImages.classList.add('is-invalid');
                productImages.focus();
            }
        });
    });
</script>
@endpush
