<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Brand::query();

            // Apply filters
            if ($request->has('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginate results
            $brands = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $brands,
                'pagination' => [
                    'current_page' => $brands->currentPage(),
                    'last_page' => $brands->lastPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch brands.',
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
                'website' => 'nullable|url',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('uploads/brands'), $logoName);
                $logoPath = 'uploads/brands/' . $logoName;
            }

            // Generate slug
            $slug = Str::slug($request->name);

            // Create brand
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => $slug,
                'description' => $request->description,
                'website' => $request->website,
                'logo' => $logoPath,
                'status' => $request->status,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => $brand,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand.',
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
            $brand = Brand::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $brand,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found.',
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
            // Find the brand
            $brand = Brand::findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'sometimes|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($brand->logo && file_exists(public_path($brand->logo))) {
                    unlink(public_path($brand->logo));
                }

                $logo = $request->file('logo');
                $logoName = time() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('uploads/brands'), $logoName);
                $request->merge(['logo' => 'uploads/brands/' . $logoName]);
            }

            // Generate slug if name is provided
            if ($request->has('name')) {
                $request->merge(['slug' => Str::slug($request->name)]);
            }

            // Update brand
            $brand->update($request->only([
                'name', 'slug', 'description', 'website', 'logo', 'status'
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'data' => $brand,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand.',
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
            // Find the brand
            $brand = Brand::findOrFail($id);

            // Check if the brand has associated products
            if ($brand->products()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete brand with associated products.',
                ], 422);
            }

            // Delete logo
            if ($brand->logo && file_exists(public_path($brand->logo))) {
                unlink(public_path($brand->logo));
            }

            // Delete brand
            $brand->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
