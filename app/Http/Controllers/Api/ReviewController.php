<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\RatingReview;
use App\Models\Product;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = RatingReview::query();

            // Apply filters
            if ($request->has('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->has('rating')) {
                $query->where('rating', $request->rating);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Include related data
            $query->with(['product', 'user']);

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $reviews = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reviews.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get reviews for a specific product.
     */
    public function getProductReviews($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $reviews = $product->ratingsReviews()
                ->with('user')
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $reviews,
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product reviews.',
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
            // Validate the request
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'user_id' => 'required|exists:users,id',
                'rating' => 'required|integer|min:1|max:5',
                'review' => 'nullable|string',
                'status' => 'nullable|in:pending,approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create the review
            $review = RatingReview::create([
                'product_id' => $request->product_id,
                'user_id' => $request->user_id,
                'rating' => $request->rating,
                'review' => $request->review,
                'status' => $request->status ?? 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review created successfully.',
                'data' => $review->load(['product', 'user']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create review.',
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
            $review = RatingReview::with(['product', 'user'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $review,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $review = RatingReview::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'rating' => 'nullable|integer|min:1|max:5',
                'review' => 'nullable|string',
                'status' => 'nullable|in:pending,approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update the review
            $review->update($request->only(['rating', 'review', 'status']));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review updated successfully.',
                'data' => $review->load(['product', 'user']),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $review = RatingReview::findOrFail($id);
            $review->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Review deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete review.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
