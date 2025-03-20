<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\CustomerResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    /*
    * Login customer
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $customer = Customer::where('phone', $request->phone)->first();
        if ($customer && Hash::check($request->password, $customer->password)) {
            $token = $customer->createToken('customer-token')->plainTextToken;

            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'customer' => $customer,
            ], 200);
        }
        return response()->json([
            'message' => 'Invalid phone or password',
        ], 401);
    }
    /*register customer
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|unique:customers',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $customer = new Customer();
        $customer->phone = $request->phone;
        $customer->password = Hash::make($request->password);
        $customer->save();
        $token = $customer->createToken('customer-token')->plainTextToken;

        return response()->json([
            'message' => 'Register successful',
            'token' => $token,
            'customer' => $customer,
        ], 200);
    }
    /*
    * Get All customers
    * @return \Illuminate\Http\JsonResponse
    */
    public function getAllCustomers()
    {
        try {
            $customers  = Customer::orderBy('id', 'desc')->limit(10)->get();
            $customers = CustomerResource::collection($customers);
            if ($customers == null) {
                return response()->json(['message' => 'No customers found'], 404);
            }
            return response()->json($customers, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
    /*
    * Get customer by id
    * @param $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function getCustomerDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 403);
        }

        try {
            $customer = Customer::where('id', $request->id)->first();

            if ($customer == null) {
                return response()->json(['message' => 'Customer not found'], 404);
            }

            $customer = new CustomerResource($customer);

            return response()->json($customer, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}
