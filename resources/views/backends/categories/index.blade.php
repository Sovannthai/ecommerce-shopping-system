@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">List Category</label>
            @include('backends.components.modal.action_btn_modal', [
                'route_create' => 'categories.create',
                'modal_name' => 'modal_category',
            ])
        </div>
        <div class="card-body">
            <div class="categoryList">
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
                                    @include('backends.components.modal.action_btn_modal', [
                                        'route_edit' => 'categories.edit',
                                        'route_delete' => 'categories.destroy',
                                        'row' => $category,
                                        'modal_name' => 'modal_category',
                                    ])
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @include('backends.components.modal.main_modal', ['modal_name' => 'modal_category']);
@endsection
