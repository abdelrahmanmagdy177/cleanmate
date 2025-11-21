<?php

namespace App\Services\Admin;

use App\Models\Area;
use Illuminate\Database\Eloquent\Collection;

class AreaService
{
    /**
     * Get all areas, optionally filtered by zone.
     *
     * @param int|null $zoneId
     * @return Collection
     */
    public function getAllAreas(?int $zoneId = null): Collection
    {
        $query = Area::with('zone');

        if ($zoneId) {
            $query->where('zone_id', $zoneId);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Create a new area.
     *
     * @param array $data
     * @return Area
     */
    public function createArea(array $data): Area
    {
        return Area::create($data);
    }

    /**
     * Get an area by ID.
     *
     * @param int $id
     * @return Area|null
     */
    public function getArea(int $id): ?Area
    {
        return Area::with('zone')->find($id);
    }

    /**
     * Update an area.
     *
     * @param int $id
     * @param array $data
     * @return Area|null
     */
    public function updateArea(int $id, array $data): ?Area
    {
        $area = Area::find($id);

        if (!$area) {
            return null;
        }

        $area->update($data);

        return $area;
    }

    /**
     * Delete an area.
     *
     * @param int $id
     * @return bool
     */
    public function deleteArea(int $id): bool
    {
        $area = Area::find($id);

        if (!$area) {
            return false;
        }

        return $area->delete();
    }

    /**
     * Toggle area status.
     *
     * @param int $id
     * @return Area|null
     */
    public function toggleStatus(int $id): ?Area
    {
        $area = Area::find($id);

        if (!$area) {
            return null;
        }

        $area->update(['is_active' => !$area->is_active]);

        return $area;
    }
}
