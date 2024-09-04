{{-- Edit Modal --}}
<div class="modal fade" id="editModal-{{ $customer->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Customer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form action="{{ route('customers.update', $customer->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="col-sm-4">
                            <label for="fname-{{ $customer->id }}" class="col-form-label">First Name</label>
                            <input type="text" class="form-control" id="fname-{{ $customer->id }}" name="fname"
                                value="{{ $customer->fname }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="lname-{{ $customer->id }}" class="col-form-label">Last Name</label>
                            <input type="text" class="form-control" id="lname-{{ $customer->id }}" name="lname"
                                value="{{ $customer->lname }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="gender-{{ $customer->id }}" class="col-form-label">Gender</label>
                            <input type="text" class="form-control" id="gender-{{ $customer->id }}" name="gender"
                                value="{{ $customer->gender }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="email-{{ $customer->id }}" class="col-form-label">Email</label>
                            <input type="email" class="form-control" id="email-{{ $customer->id }}" name="email"
                                value="{{ $customer->email }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="phone" class="col-form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                value="{{ $customer->phone }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="dob-{{ $customer->id }}" class="col-form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob-{{ $customer->id }}" name="dob"
                                value="{{ \Carbon\Carbon::parse($customer->dob)->format('Y-m-d') }}">
                        </div>
                        <div class="col-sm-4">
                            <label for="status-{{ $customer->id }}" class="col-form-label">Status</label>
                            <select class="form-control" id="status-{{ $customer->id }}" name="status">
                                <option value="1" {{ $customer->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $customer->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-sm-12">
                            <label for="address-{{ $customer->id }}" class="col-form-label">Address</label>
                            <textarea class="form-control" rows="4" id="address-{{ $customer->id }}" name="address">{{ $customer->address }}</textarea>
                        </div>
                        <div class="col-sm-12">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            @if (isset($customer) && $customer->image)
                                <img src="{{ asset('uploads/all_photo/' . $customer->image) }}"
                                    alt="{{ $customer->title }}" width="100" class="mt-2">
                            @endif
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary float-end ml-2">Update</button>
                            <button type="button" class="btn btn-dark float-end"
                                data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
