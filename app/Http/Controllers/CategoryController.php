<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use App\Http\Requests\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreCategoryRequest;

class CategoryController extends Controller
{
    /**
     * ImageService instance
     */
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('backends.categories.index', compact('categories'));
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws Exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required',
        ])->validate();

        if ($validated['name'] == null) {
            return response()->json([
                'success' => false,
                'msg'     => 'Category name is required'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $category              = new Category();
            $category->name        = $request->name;
            $category->description = $request->description;
            $category->image       = $this->imageService->uploadImage($request->file('image'));
            $category->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg'     => 'Category created successfully',
                'data'    => $category
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'msg'     => $e
            ]);
        }
    }
    /**
     * Edit the specified resource.
     */
    public function edit(Category $category)
    {
        return view('backends.categories.edit', compact('category'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        try {
            DB::beginTransaction();
            $category->name        = $request->name;
            $category->description = $request->description;
            if ($request->hasFile('image')) {
                if ($category->image) {
                    $this->imageService->deleteImage($category->image);
                }
                $category->image = $this->imageService->uploadImage($request->file('image'));
            }
            $category->save();

            DB::commit();

            $output = [
                'success' => true,
                'msg'     => 'Category updated successfully',
                'data'    => $category
            ];
        } catch (Exception $e) {
            DB::rollBack();
            $output = [
                'success' => false,
                'msg'     => $e
            ];
        }
        return response()->json($output);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->image) {
                $this->imageService->deleteImage($category->image);
            }   
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
        } catch (Exception $e) {
            return redirect()->route('categories.index')->with('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }
}
