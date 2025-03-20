@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Orders List</label>
            <a href="{{ route('orders.dashboard') }}" class="btn btn-info float-right text-uppercase btn-sm ms-2">
                <i class="fa fa-chart-bar ambitious-padding-btn"> @lang('Dashboard')</i>
            </a>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <table id="basic-datatables" class="table table-hover table-bordered text-nowrap table-responsive-lg">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>#{{ $order->order_number }}</td>
                            <td>{{ $order->user->name ?? 'Guest' }}</td>
                            <td>{{ $order->orderItems->count() }}</td>
                            <td>${{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @if($order->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->status == 'processing')
                                    <span class="badge bg-info">Processing</span>
                                @elseif($order->status == 'shipped')
                                    <span class="badge bg-primary">Shipped</span>
                                @elseif($order->status == 'delivered')
                                    <span class="badge bg-success">Delivered</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($order->payment_status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fa fa-eye ambitious-padding-btn">@lang('View')</i>
                                    </a>

                                    @if($order->status != 'cancelled' && $order->status != 'delivered')
                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline-block">
                                            @csrf
                                            <button type="button" class="btn btn-outline-danger btn-sm cancel-btn"
                                                data-form-id="cancelForm-{{ $order->id }}" title="@lang('Cancel')">
                                                <i class="fa fa-ban ambitious-padding-btn">@lang('Cancel')</i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script for cancel confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('.cancel-btn');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel this order? This will restore the product stock.')) {
                        this.closest('form').submit();
                    }
                });
            });
        });
    </script>
@endsection
