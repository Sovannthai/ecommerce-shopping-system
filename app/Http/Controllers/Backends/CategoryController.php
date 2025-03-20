<?php

namespace App\Http\Controllers\Backends;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Get all categories with parent relationship loaded
            $categories = Category::with('parent')->orderBy('id', 'desc')->get();
            return view('backends.categories.index', compact('categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backends.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $categoryData = [
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id ? $request->parent_id : null,
                'status' => $request->status,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/categories'), $imageName);
                $categoryData['image'] = 'uploads/categories/' . $imageName;
            }

            Category::create($categoryData);

            DB::commit();
            return redirect()->route('categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error creating category: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        try {
            // Eager load subcategories and parent relationship
            $category->load(['subcategories' => function($query) {
                $query->orderBy('name', 'asc');
            }, 'parent']);

            return view('backends.categories.show', compact('category'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        try {
            $categories = Category::where('id', '!=', $category->id)->get();
            return view('backends.categories.edit', compact('category', 'categories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $categoryData = [
                'name' => $request->name,
                'description' => $request->description,
                'parent_id' => $request->parent_id ? $request->parent_id : null,
                'status' => $request->status,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                // Remove old image if exists
                if ($category->image && file_exists(public_path($category->image))) {
                    unlink(public_path($category->image));
                }

                $image = $request->file('image');
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/categories'), $imageName);
                $categoryData['image'] = 'uploads/categories/' . $imageName;
            }

            $category->update($categoryData);

            DB::commit();
            return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error updating category: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        DB::beginTransaction();
        try {
            // Check if category has subcategories
            $hasSubcategories = Category::where('parent_id', $category->id)->exists();
            if ($hasSubcategories) {
                return redirect()->back()->with('error', 'Cannot delete category with subcategories.');
            }

            // Delete category image from storage
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $category->delete();

            DB::commit();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error deleting category: ' . $e->getMessage());
        }
    }

    /**
     * Display a tree of categories.
     */
    public function tree()
    {
        try {
            // Get all parent categories (those without a parent)
            $parentCategories = Category::whereNull('parent_id')
                ->with('subcategories')
                ->orderBy('name', 'asc')
                ->get();

            return view('backends.categories.tree', compact('parentCategories'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
