<?php

namespace App\Services;

use App\Models\Timeslot;
use App\Models\TimeslotOrder;
use Carbon\Carbon;

class TimeslotService
{
    /**
     * Get available timeslots for a specific date and area.
     */
    public function getAvailableTimeslots(string $date, int $areaId): array
    {
        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        
        $timeslots = Timeslot::where('is_active', true)
            ->where('day', $dayOfWeek)
            ->where('area_id', $areaId)
            ->get();
            
        $availableSlots = [];

        foreach ($timeslots as $slot) {
            $bookedCount = $this->getBookedCount($slot->id, $date);

            if ($bookedCount < $slot->capacity) {
                $availableSlots[] = [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'remaining_capacity' => $slot->capacity - $bookedCount
                ];
            }
        }
        
        return $availableSlots;
    }

    /**
     * Get booked count for a timeslot on a specific date.
     */
    public function getBookedCount(int $timeslotId, string $date): int
    {
        return TimeslotOrder::where('timeslot_id', $timeslotId)
            ->where('date', $date)
            ->count();
    }

    /**
     * Check if timeslot has available capacity.
     */
    public function hasCapacity(int $timeslotId, string $date): bool
    {
        $timeslot = Timeslot::find($timeslotId);
        
        if (!$timeslot) {
            return false;
        }

        $bookedCount = $this->getBookedCount($timeslotId, $date);
        
        return $bookedCount < $timeslot->capacity;
    }
}
