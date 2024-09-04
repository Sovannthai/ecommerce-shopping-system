<?php

namespace App\Http\Controllers\Backends;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function index()
    {
        $customers = Customer::latest()->get();
        return view('backends.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        try {
            $customer = Customer::create($request->except('image'));
            if ($request->hasFile('image')) {
                if ($customer->image) {
                    $this->imageService->deleteImage($customer->image);
                }
                $customer->image = $this->imageService->uploadImage($request->file('image'));
                $customer->save();
            }

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            return redirect()->route('customers.index')->with('error', $e->getMessage());
        }
    }


    public function show(Customer $customer)
    {
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->fill($request->except('image'));
            if ($request->hasFile('image')) {
                if ($customer->image) {
                    $this->imageService->deleteImage($customer->image);
                }
                $customer->image = $this->imageService->uploadImage($request->file('image'));
            }
            $customer->save();
            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            return redirect()->route('customers.index')->with('error', $e->getMessage());
        }
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
