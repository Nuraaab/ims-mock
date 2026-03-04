<?php

namespace Modules\IMS\Services\Measurment;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\IMS\Models\Measurement;

class MeasurmentService
{
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Measurement::query()
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function create(array $payload): Measurement
    {
        return Measurement::create([
            'name' => $payload['name'],
            'name_plural' => $payload['name_plural'] ?? null,
            'symbol' => $payload['symbol'] ?? null,
        ]);
    }

    public function find(int $id): Measurement
    {
        return Measurement::query()->findOrFail($id);
    }

    public function update(int $id, array $payload): Measurement
    {
        $measurement = Measurement::query()->findOrFail($id);
        $measurement->update([
            'name' => $payload['name'] ?? $measurement->name,
            'name_plural' => array_key_exists('name_plural', $payload) ? $payload['name_plural'] : $measurement->name_plural,
            'symbol' => array_key_exists('symbol', $payload) ? $payload['symbol'] : $measurement->symbol,
        ]);

        return $measurement;
    }

    public function delete(int $id): void
    {
        $measurement = Measurement::query()->findOrFail($id);
        $measurement->delete();
    }
}
