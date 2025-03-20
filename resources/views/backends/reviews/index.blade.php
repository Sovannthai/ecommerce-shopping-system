@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Product Reviews</label>
            <a href="#" data-toggle="modal" data-target="#create"
                class="btn btn-primary float-right text-uppercase btn-sm">
                <i class="fas fa-plus"> @lang('Add Review')</i>
            </a>
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
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $review->product->name ?? 'N/A' }}</td>
                            <td>{{ $review->user->name ?? 'N/A' }}</td>
                            <td>
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-muted"></i>
                                    @endif
                                @endfor
                                ({{ $review->rating }})
                            </td>
                            <td>{{ Str::limit($review->review, 50) }}</td>
                            <td>
                                @if($review->status == 'approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($review->status == 'rejected')
                                    <span class="badge badge-danger">Rejected</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                            <td>{{ $review->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="#" data-toggle="modal" data-target="#edit-{{ $review->id }}"
                                    class="btn btn-outline-primary btn-sm">Edit</a>

                                @if($review->status != 'approved')
                                    <form action="{{ route('reviews.approve', $review->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success btn-sm">Approve</button>
                                    </form>
                                @endif

                                @if($review->status != 'rejected')
                                    <form action="{{ route('reviews.reject', $review->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-warning btn-sm">Reject</button>
                                    </form>
                                @endif

                                <form action="{{ route('reviews.destroy', ['review' => $review->id]) }}"
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

    <!-- Create Modal -->
    @include('backends.reviews.create')

    <!-- Edit Modals -->
    @foreach ($reviews as $review)
        @include('backends.reviews.edit', ['review' => $review])
    @endforeach

    <!-- Script for delete confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this review?')) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
@endsection
