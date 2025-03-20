<?php

namespace App\Http\Controllers\Backends;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderAddress;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user', 'orderItems'])->latest()->get();
        return view('backends.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'orderAddress', 'payment']);
        return view('backends.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('orders.show', $order->id)->with('success', 'Order status updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating order status: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        DB::beginTransaction();
        try {
            // Restore stock for each order item
            foreach ($order->orderItems as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->update([
                        'stock' => $product->stock + $item->quantity
                    ]);
                }
            }

            // Update order status to cancelled
            $order->update([
                'status' => 'cancelled',
            ]);

            DB::commit();
            return redirect()->route('orders.show', $order->id)->with('success', 'Order cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error cancelling order: ' . $e->getMessage());
        }
    }

    /**
     * Display the orders dashboard with statistics.
     */
    public function dashboard()
    {
        // Total orders count
        $totalOrders = Order::count();

        // Orders by status
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $shippedOrders = Order::where('status', 'shipped')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // Total revenue
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');

        // Recent orders
        $recentOrders = Order::with(['user'])->latest()->take(10)->get();

        return view('backends.orders.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'processingOrders',
            'shippedOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalRevenue',
            'recentOrders'
        ));
    }
}
