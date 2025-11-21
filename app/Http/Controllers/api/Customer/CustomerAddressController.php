<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerAddressController extends Controller
{
    protected AddressService $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $addresses = $this->addressService->getByCustomerEmail($request->customer_email);
        return response()->json(['data' => $addresses]);
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

        $address = $this->addressService->createAddress($request->customer_email, $request->all());

        return response()->json(['message' => 'Address created successfully', 'data' => $address], 201);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'title' => 'string',
            'address_details' => 'string',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $address = $this->addressService->updateAddress($id, $request->only(['name', 'title', 'address_details', 'is_default']));

        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        return response()->json(['message' => 'Address updated successfully', 'data' => $address]);
    }

    public function destroy($id)
    {
        $deleted = $this->addressService->deleteAddress($id);

        if (!$deleted) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
