@extends('backends.master')

@section('contents')
<style>
    img{
        border-radius: 50%;
    }
</style>
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Customer</label>
            <a href="#" data-toggle="modal" data-target="#createCustomerModal"
                class="btn btn-primary float-right text-uppercase btn-sm" data-value="veiw">
                <i class="fas fa-plus"> @lang('Add')</i></a>
        </div>
        <div class="card-body">
            <table id="basic-datatables" class="table text-nowrap table-hover table-responsive-lg">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span>
                                    <a class="example-image-link" href="{{ asset('uploads/all_photo/' . $customer->image) }}"
                                        data-lightbox="lightbox-{{ $customer->id }}">
                                        <img class="example-image image-thumbnail"
                                            src="{{ asset('uploads/all_photo/' . $customer->image) }}" alt="profile"
                                            width="50px" height="50px" style="cursor:pointer" />
                                    </a>
                                </span>
                            </td>
                            <td>{{ $customer->fname }}</td>
                            <td>{{ $customer->lname }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#editModal-{{ $customer->id }}"
                                    class="btn btn-outline-primary btn-sm">Edit</a>
                                <form action="{{ route('customers.destroy', ['customer' => $customer->id]) }}"
                                    method="POST" class="delete-btn" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @include('backends.customer.edit', ['customer' => $customer])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    {{-- Create Modal --}}
    <div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog"
        aria-labelledby="createCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCustomerModalLabel">Add Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('customers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="fname" class="col-form-label">First Name</label>
                                <input type="text" class="form-control" id="fname" name="fname"
                                    value="{{ old('fname') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="lname" class="col-form-label">Last Name</label>
                                <input type="text" class="form-control" id="lname" name="lname"
                                    value="{{ old('lname') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="gender" class="col-form-label">Gender</label>
                                <input type="text" class="form-control" id="gender" name="gender"
                                    value="{{ old('gender') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="email" class="col-form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="phone" class="col-form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ old('phone') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="dob" class="col-form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="dob" name="dob"
                                    value="{{ old('dob') }}">
                            </div>
                            <div class="col-sm-4">
                                <label for="status" class="col-form-label">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <label for="address" class="col-form-label">Address</label>
                                <textarea class="form-control" rows="4" id="address" name="address">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-sm-12">
                                <label for="image">Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary float-end ml-2">Save</button>
                            <button type="button" class="btn btn-dark float-end" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
