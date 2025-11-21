<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'customer_address_id',
        'service_id',
        'variant_id',
        'order_date',
        'status',
        'service_price',
        'vat_rate',
        'vat_amount',
        'total_price',
        'payment_method',
        'payment_status',
        'notes',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress()
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workers()
    {
        return $this->belongsToMany(Worker::class, 'order_worker')
            ->withPivot('status', 'assigned_at')
            ->withTimestamps();
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    public function updateStatus($status, $byType, $byId = null)
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $status]);

        $this->statusHistory()->create([
            'status' => $status,
            'changed_by_type' => $byType,
            'changed_by_id' => $byId,
        ]);

        // Dispatch event for notification
        event(new \App\Events\OrderStatusUpdated($this, $oldStatus, $status, $byType, $byId));
    }
    public function variant()
    {
        return $this->belongsTo(ServiceVariant::class);
    }

    public function timeslots()
    {
        return $this->belongsToMany(Timeslot::class, 'timeslot_orders')
            ->withPivot('date')
            ->withTimestamps();
    }


    /**
     * Calculate VAT amount based on subtotal
     */
    public function calculateVAT(float $subtotal): float
    {
        $vatRate = config('cleanmate.vat_rate', 14.00);
        return round(($subtotal * $vatRate) / 100, 2);
    }

    /**
     * Calculate and set all price components
     */
    public function calculatePricing(float $servicePrice): void
    {
        $this->service_price = $servicePrice;
        $this->vat_rate = config('cleanmate.vat_rate', 14.00);
        
        $this->vat_amount = $this->calculateVAT($servicePrice);
        $this->total_price = $servicePrice + $this->vat_amount;
    }

    /**
     * Get price breakdown as array
     */
    public function getPriceBreakdown(): array
    {
        return [
            'service_price' => (float) $this->service_price,
            'vat_rate' => (float) $this->vat_rate,
            'vat_amount' => (float) $this->vat_amount,
            'total_price' => (float) $this->total_price,
        ];
    }
}
