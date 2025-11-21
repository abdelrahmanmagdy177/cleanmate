<?php

namespace App\Services\Admin;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Collection;

class ZoneService
{
    /**
     * Get all zones.
     *
     * @return Collection
     */
    public function getAllZones(): Collection
    {
        return Zone::with('areas')->orderBy('name')->get();
    }

    /**
     * Create a new zone.
     *
     * @param array $data
     * @return Zone
     */
    public function createZone(array $data): Zone
    {
        return Zone::create($data);
    }

    /**
     * Get a zone by ID.
     *
     * @param int $id
     * @return Zone|null
     */
    public function getZone(int $id): ?Zone
    {
        return Zone::with('areas')->find($id);
    }

    /**
     * Update a zone.
     *
     * @param int $id
     * @param array $data
     * @return Zone|null
     */
    public function updateZone(int $id, array $data): ?Zone
    {
        $zone = Zone::find($id);

        if (!$zone) {
            return null;
        }

        $zone->update($data);

        return $zone;
    }

    /**
     * Delete a zone.
     *
     * @param int $id
     * @return bool
     */
    public function deleteZone(int $id): bool
    {
        $zone = Zone::find($id);

        if (!$zone) {
            return false;
        }

        return $zone->delete();
    }

    /**
     * Toggle zone status.
     *
     * @param int $id
     * @return Zone|null
     */
    public function toggleStatus(int $id): ?Zone
    {
        $zone = Zone::find($id);

        if (!$zone) {
            return null;
        }

        $zone->update(['is_active' => !$zone->is_active]);

        return $zone;
    }
}
