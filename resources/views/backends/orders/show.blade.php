@extends('backends.master')
@section('contents')
    <div class="card">
        <div class="card-header">
            <label class="card-title font-weight-bold mb-1 text-uppercase">Order Details #{{ $order->order_number }}</label>
            <div class="float-right">
                <a href="{{ route('orders.index') }}" class="btn btn-secondary text-uppercase btn-sm">
                    <i class="fa fa-list ambitious-padding-btn"> @lang('Back to List')</i>
                </a>
            </div>
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

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 200px;">Order Number</th>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Customer</th>
                                        <td>{{ $order->user->name ?? 'Guest User' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $order->user->email ?? $order->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Date</th>
                                        <td>{{ $order->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Order Status</th>
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
                                    </tr>
                                    <tr>
                                        <th>Payment Status</th>
                                        <td>
                                            @if($order->payment_status == 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($order->payment_status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($order->payment_status == 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Order Address</h5>
                        </div>
                        <div class="card-body">
                            @if($order->orderAddress)
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 200px;">Full Name</th>
                                            <td>{{ $order->orderAddress->full_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>
                                                {{ $order->orderAddress->address_line1 }}
                                                @if($order->orderAddress->address_line2)
                                                    <br>{{ $order->orderAddress->address_line2 }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>City</th>
                                            <td>{{ $order->orderAddress->city }}</td>
                                        </tr>
                                        <tr>
                                            <th>State</th>
                                            <td>{{ $order->orderAddress->state }}</td>
                                        </tr>
                                        <tr>
                                            <th>Postal Code</th>
                                            <td>{{ $order->orderAddress->postal_code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Country</th>
                                            <td>{{ $order->orderAddress->country }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $order->orderAddress->phone }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @else
                                <div class="alert alert-info">No address information available</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Image</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->product->name ?? 'Product Deleted' }}</td>
                                        <td>
                                            @if($item->product && $item->product->main_image)
                                                <img src="{{ asset($item->product->main_image) }}" alt="{{ $item->product->name }}" width="50">
                                            @else
                                                <span class="badge bg-secondary">No Image</span>
                                            @endif
                                        </td>
                                        <td>${{ number_format($item->price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                    <td>${{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Tax:</strong></td>
                                    <td>${{ number_format($order->tax, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Shipping:</strong></td>
                                    <td>${{ number_format($order->shipping, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                                    <td>-${{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($order->payment)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 200px;">Payment Method</th>
                                    <td>{{ ucfirst($order->payment->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <th>Transaction ID</th>
                                    <td>{{ $order->payment->transaction_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Status</th>
                                    <td>
                                        @if($order->payment->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($order->payment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($order->payment->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>{{ $order->payment->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Order Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <select class="form-select" name="status" id="status">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Update Status</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            @if($order->status != 'cancelled' && $order->status != 'delivered')
                                <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-danger cancel-btn">Cancel Order</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for cancel confirmation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelBtn = document.querySelector('.cancel-btn');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel this order? This will restore the product stock.')) {
                        this.closest('form').submit();
                    }
                });
            }
        });
    </script>
@endsection
