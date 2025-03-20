@extends('backends.master')
@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title font-weight-bold text-uppercase mb-0">Category Details</h5>
            <div>
                <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-list"></i> Back to List
                </a>
                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info btn-sm">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Name</th>
                            <td>{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $category->description }}</td>
                        </tr>
                        <tr>
                            <th>Parent Category</th>
                            <td>{{ $category->parent->name ?? 'None (Top Level)' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($category->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $category->created_at->format('d M Y, h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $category->updated_at->format('d M Y, h:i A') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    @if($category->image)
                        <div class="text-center">
                            <h5>Category Image</h5>
                            <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" class="img-fluid img-thumbnail" style="max-height: 300px;">
                        </div>
                    @else
                        <div class="alert alert-info">No image available for this category.</div>
                    @endif
                </div>
            </div>

            @if($category->subcategories->count() > 0)
                <div class="mt-4">
                    <h5>Subcategories</h5>
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->subcategories as $subcategory)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $subcategory->name }}</td>
                                    <td>{{ Str::limit($subcategory->description, 50) }}</td>
                                    <td>
                                        @if($subcategory->status == 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('categories.show', $subcategory->id) }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
