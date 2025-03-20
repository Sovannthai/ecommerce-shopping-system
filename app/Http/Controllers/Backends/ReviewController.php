<?php

namespace App\Http\Controllers\Backends;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RatingReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = RatingReview::with(['product', 'user'])->latest()->get();
        return view('backends.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $users = User::all();
        return view('backends.reviews.create', compact('products', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Create the review
            RatingReview::create([
                'product_id' => $request->product_id,
                'user_id' => $request->user_id,
                'rating' => $request->rating,
                'review' => $request->review,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('reviews.index')->with('success', 'Review created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error creating review: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RatingReview $review)
    {
        $review->load(['product', 'user']);
        return view('backends.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RatingReview $review)
    {
        $products = Product::all();
        $users = User::all();
        $review->load(['product', 'user']);
        return view('backends.reviews.edit', compact('review', 'products', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RatingReview $review)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update the review
            $review->update([
                'product_id' => $request->product_id,
                'user_id' => $request->user_id,
                'rating' => $request->rating,
                'review' => $request->review,
                'status' => $request->status,
            ]);

            DB::commit();
            return redirect()->route('reviews.index')->with('success', 'Review updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating review: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RatingReview $review)
    {
        DB::beginTransaction();
        try {
            $review->delete();

            DB::commit();
            return redirect()->route('reviews.index')->with('success', 'Review deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting review: ' . $e->getMessage());
        }
    }

    /**
     * Approve a review
     */
    public function approve(RatingReview $review)
    {
        DB::beginTransaction();
        try {
            $review->update(['status' => 'approved']);

            DB::commit();
            return redirect()->back()->with('success', 'Review approved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error approving review: ' . $e->getMessage());
        }
    }

    /**
     * Reject a review
     */
    public function reject(RatingReview $review)
    {
        DB::beginTransaction();
        try {
            $review->update(['status' => 'rejected']);

            DB::commit();
            return redirect()->back()->with('success', 'Review rejected successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error rejecting review: ' . $e->getMessage());
        }
    }
}
