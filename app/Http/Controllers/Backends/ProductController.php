<?php

namespace App\Http\Controllers\Backends;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'brand'])->latest()->get();
        $categories = Category::all();
        $brands = Brand::all();
        return view('backends.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('backends.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Generate slug and SKU
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
                'is_featured' => $request->has('is_featured'),
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

                    $isPrimary = $key === 0; // First image is primary by default

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/' . $imageName,
                        'is_primary' => $isPrimary,
                        'display_order' => $key,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error creating product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'productImages', 'variants', 'ratingsReviews']);
        return view('backends.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        $product->load(['productImages']);
        return view('backends.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
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
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Update the product
            $product->update([
                'name' => $request->name,
                'category_id' => $request->category_id,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'cost_price' => $request->cost_price,
                'stock' => $request->stock,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'brand_id' => $request->brand_id,
                'is_featured' => $request->has('is_featured'),
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

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => 'uploads/products/' . $imageName,
                        'is_primary' => false,
                        'display_order' => $product->productImages()->count() + $key,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete product images from storage
            foreach ($product->productImages as $image) {
                if (file_exists(public_path($image->image))) {
                    unlink(public_path($image->image));
                }
            }

            $product->delete();
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Set image as primary
     */
    public function setPrimaryImage(Request $request, $productId, $imageId)
    {
        DB::beginTransaction();
        try {
            // Reset all product images to non-primary
            ProductImage::where('product_id', $productId)->update(['is_primary' => false]);

            // Set the selected image as primary
            ProductImage::findOrFail($imageId)->update(['is_primary' => true]);

            DB::commit();
            return redirect()->back()->with('success', 'Primary image updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating primary image: ' . $e->getMessage());
        }
    }

    /**
     * Delete product image
     */
    public function deleteImage($imageId)
    {
        DB::beginTransaction();
        try {
            $image = ProductImage::findOrFail($imageId);

            // Delete the image file
            if (file_exists(public_path($image->image))) {
                unlink(public_path($image->image));
            }

            $image->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting image: ' . $e->getMessage());
        }
    }
}
