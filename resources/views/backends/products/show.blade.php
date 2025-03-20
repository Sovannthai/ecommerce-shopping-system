@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Product Details</label>
            <div class="float-right">
                <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary text-uppercase btn-sm">
                    <i class="fa fa-edit ambitious-padding-btn text-uppercase"> @lang('Edit')</i>
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-secondary text-uppercase btn-sm">
                    <i class="fa fa-arrow-left ambitious-padding-btn text-uppercase"> @lang('Back')</i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <!-- Product Images Carousel -->
                    <div id="productCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->productImages as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ asset($image->image) }}" class="d-block w-100" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                            @if($product->productImages->count() == 0)
                                <div class="carousel-item active">
                                    <img src="{{ asset('default.jpg') }}" class="d-block w-100" alt="Default Image">
                                </div>
                            @endif
                        </div>
                        @if($product->productImages->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        @endif
                    </div>

                    <!-- Thumbnail Navigation -->
                    @if($product->productImages->count() > 1)
                        <div class="row mb-4">
                            @foreach($product->productImages as $key => $image)
                                <div class="col-3">
                                    <img src="{{ asset($image->image) }}" class="img-thumbnail"
                                        onclick="$('.carousel').carousel({{ $key }})" style="cursor: pointer;"
                                        alt="Thumbnail {{ $key + 1 }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <h2>{{ $product->name }}</h2>

                    <div class="d-flex mb-3">
                        @if($product->discount_price)
                            <h4 class="text-danger me-3">${{ $product->discount_price }}</h4>
                            <h5 class="text-decoration-line-through text-muted">${{ $product->price }}</h5>
                            <span class="badge bg-danger ms-2">{{ $product->discount_percentage }}% OFF</span>
                        @else
                            <h4>${{ $product->price }}</h4>
                        @endif
                    </div>

                    <p><strong>SKU:</strong> {{ $product->sku }}</p>
                    <p><strong>Category:</strong> {{ $product->category->name }}</p>
                    @if($product->brand)
                        <p><strong>Brand:</strong> {{ $product->brand->name }}</p>
                    @endif
                    <p><strong>Stock:</strong> {{ $product->stock }}</p>
                    <p><strong>Status:</strong>
                        <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </p>
                    @if($product->is_featured)
                        <p><span class="badge bg-warning">Featured Product</span></p>
                    @endif

                    @if($product->short_description)
                        <div class="mb-3">
                            <h5>Short Description</h5>
                            <p>{{ $product->short_description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($product->description)
                <div class="mt-4">
                    <h4>Description</h4>
                    <div class="border p-3 rounded">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif

            @if($product->variants && $product->variants->count() > 0)
                <div class="mt-4">
                    <h4>Product Variants</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>SKU</th>
                                    <th>Attributes</th>
                                    <th>Price Adjustment</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr>
                                        <td>{{ $variant->sku }}</td>
                                        <td>
                                            @foreach($variant->attributes as $attribute)
                                                <span class="badge bg-info">
                                                    {{ $attribute->productAttribute->name }}: {{ $attribute->value }}
                                                </span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($variant->price_adjustment > 0)
                                                +${{ $variant->price_adjustment }}
                                            @elseif($variant->price_adjustment < 0)
                                                -${{ abs($variant->price_adjustment) }}
                                            @else
                                                $0
                                            @endif
                                        </td>
                                        <td>{{ $variant->stock }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($product->ratingsReviews && $product->ratingsReviews->count() > 0)
                <div class="mt-4">
                    <h4>Ratings & Reviews</h4>
                    <div class="row">
                        @foreach($product->ratingsReviews->where('status', 'approved') as $review)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="card-title">{{ $review->user->name }}</h5>
                                            <div>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa fa-star{{ $i <= $review->rating ? ' text-warning' : ' text-muted' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="card-text">{{ $review->review }}</p>
                                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($product->meta_title || $product->meta_description || $product->meta_keywords)
                <div class="mt-4">
                    <h4>SEO Information</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                @if($product->meta_title)
                                    <tr>
                                        <th width="20%">Meta Title</th>
                                        <td>{{ $product->meta_title }}</td>
                                    </tr>
                                @endif
                                @if($product->meta_description)
                                    <tr>
                                        <th>Meta Description</th>
                                        <td>{{ $product->meta_description }}</td>
                                    </tr>
                                @endif
                                @if($product->meta_keywords)
                                    <tr>
                                        <th>Meta Keywords</th>
                                        <td>{{ $product->meta_keywords }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
