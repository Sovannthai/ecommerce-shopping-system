@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">List Category</label>
            <a class="btn btn-primary float-right text-uppercase btn-sm" data-bs-toggle="modal"
                data-bs-target="#create"><i class="fa fa-plus ambitious-padding-btn text-uppercase"> @lang('Add')</i></a>
        </div>
        <div class="card-body">
            <table id="basic-datatables" class="table table-hover table-bordered text-nowrap table-responsive-lg">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <a href="" class="btn btn-sm btn-outline-primary " data-bs-toggle="modal"
                                data-bs-target="#edit-{{ $category->id }}"><i
                                        class="fa fa-edit ambitious-padding-btn text-uppercase">
                                        @lang('Edit')</i></a>
                                <form id="deleteForm"
                                    action="{{ route('categories.destroy', ['category' => $category->id]) }}" method="POST"
                                    class="d-inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm delete-btn"
                                        title="@lang('Delete')">
                                        <i class="fa fa-trash ambitious-padding-btn text-uppercase">
                                            @lang('Delete')</i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @include('backends.categories.create', ['category' => $category])
                        @include('backends.categories.edit', ['category' => $category])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


