@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Edit Brand</label>
            <a href="{{ route('brands.index') }}" class="btn btn-secondary float-right text-uppercase btn-sm">
                <i class="fa fa-list ambitious-padding-btn"> @lang('Back to List')</i>
            </a>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

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
                                    <input type="file" class="form-control-file" id="logo-{{ $brand->id }}" name="logo">
                                    @if ($brand->logo)
                                        <div class="mt-2">
                                            <label>Current Logo:</label>
                                            <div class="border p-2 mt-1 text-center">
                                                <img src="{{ asset($brand->logo) }}" alt="{{ $brand->name }}" style="max-height: 100px; max-width: 100%;">
                                            </div>
                                        </div>
                                    @endif
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
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
