<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Category::query();

            // Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } else {
                // By default, fetch only parent categories
                $query->whereNull('parent_id');
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('include_children') && $request->include_children) {
                $query->with('children');
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $categories = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $categories,
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories.',
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
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/categories'), $imageName);
                $imagePath = 'uploads/categories/' . $imageName;
            }

            // Generate slug
            $slug = Str::slug($request->name);

            // Create category
            $category = Category::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'parent_id' => $request->parent_id,
                'image' => $imagePath,
                'status' => $request->status,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category.',
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
            $category = Category::with('children')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
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
            // Find the category
            $category = Category::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'parent_id' => 'nullable|exists:categories,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'sometimes|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/categories'), $imageName);
                $request->merge(['image' => 'uploads/categories/' . $imageName]);
            }

            // Generate slug if name is provided
            if ($request->has('name')) {
                $request->merge(['slug' => Str::slug($request->name)]);
            }

            // Update category
            $category->update($request->only([
                'name', 'slug', 'description', 'parent_id', 'image', 'status'
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update category.',
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
            // Find the category
            $category = Category::findOrFail($id);

            // Check if the category has children
            $hasChildren = Category::where('parent_id', $id)->exists();

            if ($hasChildren) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete category with subcategories.',
                ], 422);
            }

            // Delete image
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            // Delete category
            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all categories (tree structure).
     */
    public function tree()
    {
        try {
            $categories = Category::whereNull('parent_id')
                ->with('children')
                ->where('status', 'active')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch category tree.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
