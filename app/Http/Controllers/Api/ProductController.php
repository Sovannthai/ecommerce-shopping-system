<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query();

            // Apply filters
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('is_featured')) {
                $query->where('is_featured', $request->is_featured);
            }

            // Include related data
            $query->with(['category', 'brand', 'productImages']);

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $products = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
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
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price' => 'required|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'brand_id' => 'nullable|exists:brands,id',
                'is_featured' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'product_images' => 'nullable|array',
                'product_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'product_images.*.is_primary' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Generate slug
            $slug = Str::slug($request->name);
            $sku = 'PRD-' . strtoupper(Str::random(6));

            // Create the product
            $product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'sku' => $sku,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'cost_price' => $request->cost_price,
                'stock' => $request->stock,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'brand_id' => $request->brand_id,
                'is_featured' => $request->is_featured ?? false,
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'status' => $request->status,
            ]);

            // Handle product images
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $key => $image) {
                    $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/products'), $imageName);

                    $isPrimary = $request->input('product_images.' . $key . '.is_primary', false);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/' . $imageName,
                        'is_primary' => $isPrimary,
                        'display_order' => $key,
                    ]);
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data' => $product->load(['category', 'brand', 'productImages']),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
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
            $product = Product::with(['category', 'brand', 'productImages', 'variants', 'ratingsReviews'])->findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $product,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Find the product
            $product = Product::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'category_id' => 'sometimes|exists:categories,id',
                'price' => 'sometimes|numeric|min:0',
                'discount_price' => 'nullable|numeric|min:0',
                'cost_price' => 'nullable|numeric|min:0',
                'stock' => 'sometimes|integer|min:0',
                'description' => 'nullable|string',
                'short_description' => 'nullable|string',
                'brand_id' => 'nullable|exists:brands,id',
                'is_featured' => 'nullable|boolean',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'meta_keywords' => 'nullable|string',
                'status' => 'sometimes|in:active,inactive',
                'product_images' => 'nullable|array',
                'product_images.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'product_images.*.is_primary' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update slug if name is provided
            if ($request->has('name')) {
                $request->merge(['slug' => Str::slug($request->name)]);
            }

            // Update the product
            $product->update($request->only([
                'name', 'slug', 'category_id', 'price', 'discount_price', 'cost_price',
                'stock', 'description', 'short_description', 'brand_id', 'is_featured',
                'meta_title', 'meta_description', 'meta_keywords', 'status'
            ]));

            // Handle product images
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $key => $image) {
                    $imageName = time() . '_' . $key . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('uploads/products'), $imageName);

                    $isPrimary = $request->input('product_images.' . $key . '.is_primary', false);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/' . $imageName,
                        'is_primary' => $isPrimary,
                        'display_order' => $key,
                    ]);
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data' => $product->load(['category', 'brand', 'productImages']),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
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
            // Find and delete the product
            $product = Product::findOrFail($id);

            // Delete product images from storage
            foreach ($product->productImages as $image) {
                if (file_exists(public_path($image->image))) {
                    unlink(public_path($image->image));
                }
            }

            $product->delete();

            DB::commit(); // Commit the transaction

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
