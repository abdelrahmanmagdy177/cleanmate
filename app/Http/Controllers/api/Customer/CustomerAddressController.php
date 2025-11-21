<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('email', $request->customer_email)->first();
        return response()->json(['data' => $customer->addresses]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email', // We identify customer by email for now
            'name' => 'required|string',
            'title' => 'required|string',
            'address_details' => 'required|string',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::firstOrCreate(
            ['email' => $request->customer_email],
            ['name' => 'Unknown', 'phone' => 'Unknown'] // Placeholder if creating new
        );

        if ($request->is_default) {
            $customer->addresses()->update(['is_default' => false]);
        }

        $address = $customer->addresses()->create([
            'name' => $request->name,
            'title' => $request->title,
            'address_details' => $request->address_details,
            'is_default' => $request->is_default ?? false,
        ]);

        return response()->json(['message' => 'Address created successfully', 'data' => $address], 201);
    }

    public function update(Request $request, $id)
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'title' => 'string',
            'address_details' => 'string',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->has('is_default') && $request->is_default) {
            $address->customer->addresses()->update(['is_default' => false]);
        }

        $address->update($request->only(['name', 'title', 'address_details', 'is_default']));

        return response()->json(['message' => 'Address updated successfully', 'data' => $address]);
    }

    public function destroy($id)
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
