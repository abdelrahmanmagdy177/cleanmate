<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Models\CustomerAddress;

class AddressService
{
    /**
     * Get all addresses for a customer by email.
     *
     * @param string $email
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByCustomerEmail(string $email)
    {
        $customer = Customer::where('email', $email)->first();
        
        if (!$customer) {
            return collect([]);
        }

        return $customer->addresses;
    }

    /**
     * Create a new address for a customer.
     *
     * @param string $email
     * @param array $data
     * @return CustomerAddress
     */
    public function createAddress(string $email, array $data): CustomerAddress
    {
        $customer = Customer::firstOrCreate(
            ['email' => $email],
            ['name' => 'Unknown', 'phone' => 'Unknown'] // Placeholder if creating new
        );

        if (isset($data['is_default']) && $data['is_default']) {
            $customer->addresses()->update(['is_default' => false]);
        }

        return $customer->addresses()->create([
            'name' => $data['name'],
            'title' => $data['title'],
            'address_details' => $data['address_details'],
            'is_default' => $data['is_default'] ?? false,
        ]);
    }

    /**
     * Update an existing address.
     *
     * @param int $id
     * @param array $data
     * @return CustomerAddress|null
     */
    public function updateAddress(int $id, array $data): ?CustomerAddress
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return null;
        }

        if (isset($data['is_default']) && $data['is_default']) {
            $address->customer->addresses()->update(['is_default' => false]);
        }

        $address->update($data);

        return $address;
    }

    /**
     * Delete an address.
     *
     * @param int $id
     * @return bool
     */
    public function deleteAddress(int $id): bool
    {
        $address = CustomerAddress::find($id);

        if (!$address) {
            return false;
        }

        return $address->delete();
    }
}
