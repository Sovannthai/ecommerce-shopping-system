@extends('backends.master')

@section('contents')
<div class="card">
    <div class="card-header">
        <label class="card-title font-weight-bold mb-1 text-uppercase">Brands</label>
        <a href="#" data-toggle="modal" data-target="#createBrandModal"
            class="btn btn-primary float-right text-uppercase btn-sm" data-value="veiw">
            <i class="fas fa-plus"> @lang('Add')</i></a>
    </div>
    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <table id="basic-datatables" class="table text-nowrap table-hover table-responsive-lg">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $brand)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span>
                                @if($brand->logo)
                                    <a class="example-image-link" href="{{ asset($brand->logo) }}"
                                        data-lightbox="lightbox-{{ $brand->id }}">
                                        <img class="example-image image-thumbnail"
                                            src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}"
                                            width="50px" height="50px" style="cursor:pointer" />
                                    </a>
                                @else
                                    <div class="bg-light text-center" style="width:50px;height:50px;">
                                        <i class="fas fa-building fa-2x text-muted d-flex justify-content-center align-items-center h-100"></i>
                                    </div>
                                @endif
                            </span>
                        </td>
                        <td>{{ $brand->name }}</td>
                        <td>
                            <span class="badge bg-{{ $brand->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($brand->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('brands.show', $brand->id) }}"
                                class="btn btn-outline-info btn-sm">View</a>
                            <a href="#" data-toggle="modal" data-target="#editModal-{{ $brand->id }}"
                                class="btn btn-outline-primary btn-sm">Edit</a>
                            <form action="{{ route('brands.destroy', ['brand' => $brand->id]) }}"
                                method="POST" class="delete-btn" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Create Brand Modal --}}
<div class="modal fade" id="createBrandModal" tabindex="-1" role="dialog" aria-labelledby="createBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBrandModalLabel">Add Brand</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Brand Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="{{ old('name') }}">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="logo">Logo</label>
                        <input type="file" class="form-control-file" id="logo" name="logo">
                    </div>
                    <div class="form-group">
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Edit Brand Modals --}}
@foreach ($brands as $brand)
    <div class="modal fade" id="editModal-{{ $brand->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-{{ $brand->id }}" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel-{{ $brand->id }}">Edit Brand</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name-{{ $brand->id }}">Brand Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name-{{ $brand->id }}" name="name" required value="{{ old('name', $brand->name) }}">
                        </div>
                        <div class="form-group">
                            <label for="description-{{ $brand->id }}">Description</label>
                            <textarea class="form-control" id="description-{{ $brand->id }}" name="description" rows="3">{{ old('description', $brand->description) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="logo-{{ $brand->id }}">Logo</label>
                            @if ($brand->logo)
                                <div class="mt-2 mb-2">
                                    <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" class="img-thumbnail" width="100">
                                </div>
                            @endif
                            <input type="file" class="form-control-file" id="logo-{{ $brand->id }}" name="logo">
                            <small class="form-text text-muted">Leave empty to keep current logo</small>
                        </div>
                        <div class="form-group">
                            <label for="status-{{ $brand->id }}">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="status-{{ $brand->id }}" name="status" required>
                                <option value="active" {{ old('status', $brand->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $brand->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection
