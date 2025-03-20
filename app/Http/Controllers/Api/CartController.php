<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get the cart for a user or session
     */
    public function getCart(Request $request)
    {
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->session_id;

            if (!$userId && !$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated and no session ID provided.',
                ], 400);
            }

            // Find or create cart
            $cart = $this->findOrCreateCart($userId, $sessionId);

            // Load cart items with product details
            $cart->load(['items.product', 'items.productVariant']);

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->price * $item->quantity;
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'cart' => $cart,
                    'subtotal' => $subtotal,
                    'item_count' => $cart->items->sum('quantity')
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add item to cart
     */
    public function addItem(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'product_variant_id' => 'nullable|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
                'session_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->session_id;

            if (!$userId && !$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated and no session ID provided.',
                ], 400);
            }

            // Find or create cart
            $cart = $this->findOrCreateCart($userId, $sessionId);

            // Get product
            $product = Product::findOrFail($request->product_id);

            // Get product variant if provided
            $productVariant = null;
            if ($request->product_variant_id) {
                $productVariant = ProductVariant::findOrFail($request->product_variant_id);
            }

            // Get price
            $price = $product->discount_price ?? $product->price;
            if ($productVariant) {
                $price += $productVariant->price_adjustment;
            }

            // Check if the item already exists in the cart
            $cartItem = $cart->items()
                ->where('product_id', $request->product_id)
                ->where('product_variant_id', $request->product_variant_id)
                ->first();

            if ($cartItem) {
                // Update existing item
                $cartItem->increment('quantity', $request->quantity);
            } else {
                // Create new item
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $request->product_id,
                    'product_variant_id' => $request->product_variant_id,
                    'quantity' => $request->quantity,
                    'price' => $price
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart.',
                'data' => $cartItem,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update cart item
     */
    public function updateItem(Request $request, $itemId)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->session_id;

            if (!$userId && !$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated and no session ID provided.',
                ], 400);
            }

            // Find cart
            $cart = $this->findCart($userId, $sessionId);

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }

            // Find cart item
            $cartItem = $cart->items()->findOrFail($itemId);

            // Update quantity
            $cartItem->update([
                'quantity' => $request->quantity
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart item updated.',
                'data' => $cartItem,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart item.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(Request $request, $itemId)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->session_id;

            if (!$userId && !$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated and no session ID provided.',
                ], 400);
            }

            // Find cart
            $cart = $this->findCart($userId, $sessionId);

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }

            // Find and delete cart item
            $cartItem = $cart->items()->findOrFail($itemId);
            $cartItem->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart(Request $request)
    {
        DB::beginTransaction();
        try {
            $userId = Auth::check() ? Auth::id() : null;
            $sessionId = $request->session_id;

            if (!$userId && !$sessionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated and no session ID provided.',
                ], 400);
            }

            // Find cart
            $cart = $this->findCart($userId, $sessionId);

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart not found.',
                ], 404);
            }

            // Delete all cart items
            $cart->items()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transfer session cart to user cart after login
     */
    public function transferCart(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'session_id' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.',
                ], 401);
            }

            $userId = Auth::id();
            $sessionId = $request->session_id;

            // Find session cart
            $sessionCart = Cart::where('session_id', $sessionId)->first();

            if (!$sessionCart || $sessionCart->items()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session cart not found or empty.',
                ], 404);
            }

            // Find or create user cart
            $userCart = Cart::firstOrCreate(
                ['user_id' => $userId],
                ['session_id' => null]
            );

            // Transfer items
            foreach ($sessionCart->items as $item) {
                // Check if the item already exists in the user cart
                $existingItem = $userCart->items()
                    ->where('product_id', $item->product_id)
                    ->where('product_variant_id', $item->product_variant_id)
                    ->first();

                if ($existingItem) {
                    // Update existing item
                    $existingItem->increment('quantity', $item->quantity);
                } else {
                    // Create new item
                    CartItem::create([
                        'cart_id' => $userCart->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->product_variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ]);
                }
            }

            // Delete session cart
            $sessionCart->items()->delete();
            $sessionCart->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cart transferred successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer cart.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper method to find or create a cart
     */
    private function findOrCreateCart($userId, $sessionId)
    {
        if ($userId) {
            return Cart::firstOrCreate(
                ['user_id' => $userId],
                ['session_id' => null]
            );
        } else {
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null]
            );
        }
    }

    /**
     * Helper method to find a cart
     */
    private function findCart($userId, $sessionId)
    {
        if ($userId) {
            return Cart::where('user_id', $userId)->first();
        } else {
            return Cart::where('session_id', $sessionId)->first();
        }
    }
}
