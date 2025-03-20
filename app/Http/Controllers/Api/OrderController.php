<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $query = Order::query();
            $query->where('user_id', Auth::id());

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            // Include related data
            if ($request->has('with_items') && $request->with_items) {
                $query->with('items');
            }

            if ($request->has('with_addresses') && $request->with_addresses) {
                $query->with('addresses');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $orders = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $orders,
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            // Validate the request
            $validator = Validator::make($request->all(), [
                'shipping_address' => 'required|array',
                'shipping_address.name' => 'required|string|max:255',
                'shipping_address.phone' => 'required|string|max:20',
                'shipping_address.email' => 'required|email|max:255',
                'shipping_address.address_line1' => 'required|string|max:255',
                'shipping_address.address_line2' => 'nullable|string|max:255',
                'shipping_address.city' => 'required|string|max:100',
                'shipping_address.state' => 'required|string|max:100',
                'shipping_address.postal_code' => 'required|string|max:20',
                'shipping_address.country' => 'required|string|max:100',

                'billing_address' => 'required|array',
                'billing_address.name' => 'required|string|max:255',
                'billing_address.phone' => 'required|string|max:20',
                'billing_address.email' => 'required|email|max:255',
                'billing_address.address_line1' => 'required|string|max:255',
                'billing_address.address_line2' => 'nullable|string|max:255',
                'billing_address.city' => 'required|string|max:100',
                'billing_address.state' => 'required|string|max:100',
                'billing_address.postal_code' => 'required|string|max:20',
                'billing_address.country' => 'required|string|max:100',

                'shipping_method' => 'required|string|max:100',
                'payment_method' => 'required|string|max:100',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Get user cart
            $cart = Cart::where('user_id', Auth::id())->first();

            if (!$cart || $cart->items()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty.',
                ], 400);
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($cart->items as $item) {
                $subtotal += $item->price * $item->quantity;
            }

            // Shipping cost (could be calculated based on shipping method)
            $shippingCost = 10.00; // Example value

            // Tax calculation
            $taxRate = 0.10; // Example: 10% tax
            $tax = $subtotal * $taxRate;

            // Total amount
            $totalAmount = $subtotal + $tax + $shippingCost;

            // Generate order number
            $orderNo = 'ORD-' . strtoupper(uniqid());

            // Create the order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_no' => $orderNo,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shippingCost,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'shipping_method' => $request->shipping_method,
                'notes' => $request->notes,
            ]);

            // Create order addresses
            $shippingAddress = $request->shipping_address;
            OrderAddress::create([
                'order_id' => $order->id,
                'address_type' => 'shipping',
                'name' => $shippingAddress['name'],
                'phone' => $shippingAddress['phone'],
                'email' => $shippingAddress['email'],
                'address_line1' => $shippingAddress['address_line1'],
                'address_line2' => $shippingAddress['address_line2'] ?? null,
                'city' => $shippingAddress['city'],
                'state' => $shippingAddress['state'],
                'postal_code' => $shippingAddress['postal_code'],
                'country' => $shippingAddress['country'],
            ]);

            $billingAddress = $request->billing_address;
            OrderAddress::create([
                'order_id' => $order->id,
                'address_type' => 'billing',
                'name' => $billingAddress['name'],
                'phone' => $billingAddress['phone'],
                'email' => $billingAddress['email'],
                'address_line1' => $billingAddress['address_line1'],
                'address_line2' => $billingAddress['address_line2'] ?? null,
                'city' => $billingAddress['city'],
                'state' => $billingAddress['state'],
                'postal_code' => $billingAddress['postal_code'],
                'country' => $billingAddress['country'],
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                $product = Product::find($item->product_id);
                $variant = null;

                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                }

                // Ensure product exists
                if (!$product) {
                    continue;
                }

                // Check stock availability
                $stockToCheck = $variant ? $variant->stock : $product->stock;

                if ($stockToCheck < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Not enough stock for product: {$product->name}",
                    ], 400);
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                // Reduce stock
                if ($variant) {
                    $variant->decrement('stock', $item->quantity);
                } else {
                    $product->decrement('stock', $item->quantity);
                }
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'payment_data' => json_encode([
                    'method' => $request->payment_method,
                    'date' => now()->toDateTimeString(),
                ]),
            ]);

            // Clear the cart
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => [
                    'order' => $order->load(['items', 'addresses']),
                    'payment' => $payment,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $order = Order::with(['items.product', 'items.productVariant', 'addresses', 'payments'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Cancel an order.
     */
    public function cancel($id)
    {
        DB::beginTransaction();
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $order = Order::where('user_id', Auth::id())->findOrFail($id);

            // Check if order can be cancelled
            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled in its current state.',
                ], 400);
            }

            // Update order status
            $order->update([
                'status' => 'cancelled',
            ]);

            // Restore stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                $variant = null;

                if ($item->product_variant_id) {
                    $variant = ProductVariant::find($item->product_variant_id);
                }

                if ($variant) {
                    $variant->increment('stock', $item->quantity);
                } elseif ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully.',
                'data' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update order status (admin only).
     */
    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
                'payment_status' => 'sometimes|required|in:pending,paid,failed,refunded',
                'tracking_number' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Find the order
            $order = Order::findOrFail($id);

            // Update order fields
            $updateData = [
                'status' => $request->status,
            ];

            if ($request->has('payment_status')) {
                $updateData['payment_status'] = $request->payment_status;
            }

            if ($request->has('tracking_number')) {
                $updateData['tracking_number'] = $request->tracking_number;
            }

            $order->update($updateData);

            // If cancelled, restore stock
            if ($request->status === 'cancelled' && $order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Product::find($item->product_id);
                    $variant = null;

                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                    }

                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    } elseif ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // If payment status is updated to paid, update the payment record
            if ($request->has('payment_status') && $request->payment_status === 'paid') {
                $payment = Payment::where('order_id', $order->id)->first();

                if ($payment) {
                    $payment->update([
                        'status' => 'completed',
                        'transaction_id' => $request->transaction_id ?? uniqid('TRANS-'),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.',
                'data' => $order->fresh(),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get order statistics (admin only).
     */
    public function statistics()
    {
        try {
            // Get count by status
            $statusCounts = Order::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->get()
                ->pluck('total', 'status')
                ->toArray();

            // Get count by payment status
            $paymentStatusCounts = Order::select('payment_status', DB::raw('count(*) as total'))
                ->groupBy('payment_status')
                ->get()
                ->pluck('total', 'payment_status')
                ->toArray();

            // Get total revenue
            $totalRevenue = Order::where('payment_status', 'paid')
                ->sum('total_amount');

            // Get recent orders
            $recentOrders = Order::with(['user'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'status_counts' => $statusCounts,
                    'payment_status_counts' => $paymentStatusCounts,
                    'total_revenue' => $totalRevenue,
                    'total_orders' => Order::count(),
                    'recent_orders' => $recentOrders,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get order statistics.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
