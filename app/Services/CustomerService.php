<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    /**
     * Find or create a customer by email.
     */
    public function findOrCreateByEmail(string $email, array $data = []): Customer
    {
        return Customer::firstOrCreate(
            ['email' => $email],
            [
                'name' => $data['name'] ?? 'Unknown',
                'phone' => $data['phone'] ?? 'Unknown',
                'status' => 'active'
            ]
        );
    }

    /**
     * Find customer by email.
     */
    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    /**
     * Update customer area.
     */
    public function updateArea(int $customerId, int $areaId): bool
    {
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            return false;
        }

        return $customer->update(['area_id' => $areaId]);
    }
}
