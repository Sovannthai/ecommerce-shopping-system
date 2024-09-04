@extends('backends.master')

@section('contents')
    <div class="card">
        <div class="card-header text-uppercase">
            @lang('Users')
            <a href="" class="btn btn-primary btn-sm float-right" data-bs-toggle="modal" data-bs-target="#create">+
                @lang('Add')</a>
        </div>
        <div class="card-body">
            <table id="basic-datatables" class="table table-bordered text-nowrap table-hover table-responsive-lg">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sliders as $slider)
                        <tr>
                            <td>{{  $loop->iteration}}</td>
                            <td>{{ $slider->title }}</td>
                            <td>
                                <span>
                                    <a class="example-image-link" href="{{ asset('uploads/all_photo/' . $slider->image) }}"
                                        data-lightbox="lightbox-{{ $slider->id }}">
                                        <img class="example-image image-thumbnail"
                                            src="{{ asset('uploads/all_photo/' . $slider->image) }}" alt="profile"
                                            width="50px" height="50px" style="cursor:pointer" />
                                    </a>
                                </span>
                            </td>
                            <td>{{ $slider->order }}</td>
                            <td style="font-size: 16">
                                @if ($slider->status == 'active')
                                        <p class="badge badge-success">Active</p>
                                    @else
                                        <p class="badge badge-danger">Inactive</p>
                                    @endif
                            </td>
                            <td>
                                <a href="" class="btn btn-outline-primary btn-sm text-uppercase"
                                    data-bs-toggle="modal" data-bs-target="#sliderModal-{{ $slider->id }}"> <i
                                        class="fa fa-edit ambitious-padding-btn"> @lang('Edit')</i></a>
                                <form action="{{ route('sliders.destroy', $slider->id) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm delete-btn text-uppercase">
                                        <i class="fa fa-trash ambitious-padding-btn"> @lang('Delete')</i></button>
                                </form>
                            </td>
                        </tr>
                        @include('backends.sliders.create_and_edit', ['slider' => $slider])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
