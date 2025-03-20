@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Brand Details</label>
            <div class="float-right">
                <a href="{{ route('brands.edit', $brand->id) }}" class="btn btn-primary text-uppercase btn-sm">
                    <i class="fa fa-edit ambitious-padding-btn"> @lang('Edit')</i>
                </a>
                <a href="{{ route('brands.index') }}" class="btn btn-secondary text-uppercase btn-sm">
                    <i class="fa fa-list ambitious-padding-btn"> @lang('Back to List')</i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">Name</th>
                                <td>{{ $brand->name }}</td>
                            </tr>
                            <tr>
                                <th>Slug</th>
                                <td>{{ $brand->slug }}</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>{{ $brand->description ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($brand->status == 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $brand->created_at->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At</th>
                                <td>{{ $brand->updated_at->format('d M Y, h:i A') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Brand Logo</h5>
                        </div>
                        <div class="card-body text-center">
                            @if ($brand->logo)
                                <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="img-fluid" style="max-height: 250px;">
                            @else
                                <div class="alert alert-info">
                                    No logo available for this brand.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products associated with this brand -->
            <div class="mt-4">
                <h5 class="mb-3">Products in this Brand</h5>
                @if($brand->products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brand->products as $key => $product)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ asset($product->main_image) }}" alt="{{ $product->name }}" width="50">
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>${{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($product->status == 'active')
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-info">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        No products are associated with this brand yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
