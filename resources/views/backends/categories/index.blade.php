@extends('backends.master')

@section('contents')
<div class="card">
    <div class="card-header">
        <label class="card-title font-weight-bold mb-1 text-uppercase">Categories</label>
        <div class="float-right">
            <a href="{{ route('categories.tree') }}" class="btn btn-success text-uppercase btn-sm mr-2">
                <i class="fas fa-sitemap"> @lang('Tree View')</i>
            </a>
            <a href="#" data-toggle="modal" data-target="#create"
                class="btn btn-primary text-uppercase btn-sm" data-value="veiw">
                <i class="fas fa-plus"> @lang('Add')</i>
            </a>
        </div>
    </div>
    <div class="card-body">

        <table id="basic-datatables" class="table text-nowrap table-hover table-responsive-lg">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Parent Category</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <span>
                                @if($category->image)
                                    <a class="example-image-link" href="{{ asset($category->image) }}"
                                        data-lightbox="lightbox-{{ $category->id }}">
                                        <img class="example-image image-thumbnail"
                                            src="{{ asset($category->image) }}" alt="{{ $category->name }}"
                                            width="50px" height="50px" style="cursor:pointer" />
                                    </a>
                                @else
                                    <div class="bg-light text-center" style="width:50px;height:50px;">
                                        <i class="fas fa-folder fa-2x text-muted d-flex justify-content-center align-items-center h-100"></i>
                                    </div>
                                @endif
                            </span>
                        </td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->parent ? $category->parent->name : 'None' }}</td>
                        <td>
                            <span class="badge bg-{{ $category->status == 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('categories.show', $category->id) }}"
                                class="btn btn-outline-info btn-sm">View</a>
                            <a href="#" data-toggle="modal" data-target="#edit-{{ $category->id }}"
                                class="btn btn-outline-primary btn-sm">Edit</a>
                            <form action="{{ route('categories.destroy', ['category' => $category->id]) }}"
                                method="POST" class="delete-btn" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @include('backends.categories.edit', ['category' => $category])
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@include('backends.categories.create')
@endsection
