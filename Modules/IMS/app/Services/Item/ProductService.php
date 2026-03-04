<?php

namespace Modules\IMS\Services\Item;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Product;
use Modules\IMS\Models\ProductGroup;

class ProductService
{
    public function paginate(User $actor, ?int $organizationId = null, int $perPage = 15): LengthAwarePaginator
    {
        $organizationIds = scope_ids($actor, 'products.view', 'organization');
        $branchIds = scope_ids($actor, 'products.view', 'branch');

        if (empty($organizationIds) && empty($branchIds)) {
            throw ValidationException::withMessages([
                'organization_id' => ['No accessible product scope found for the current user.'],
            ]);
        }

        return Product::query()
            ->with(['item:id,name,item_type', 'productGroup:id,name', 'defaultMeasurement:id,name,symbol'])
            ->where(function ($query) use ($organizationIds, $branchIds) {
                if (! empty($organizationIds)) {
                    $query->orWhereIn('organization_id', $organizationIds);
                }

                if (! empty($branchIds)) {
                    $query->orWhereHas('productGroup', function ($productGroupQuery) use ($branchIds) {
                        $productGroupQuery->whereIn('branch_id', $branchIds);
                    });
                }
            })
            ->when($organizationId !== null, function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function create(User $actor, array $payload): Product
    {
        $organizationId = $payload['organization_id'] ?? null;

        if (! empty($payload['product_group_id'])) {
            $productGroup = ProductGroup::query()->findOrFail((int) $payload['product_group_id']);
            $organizationId = (int) $productGroup->organization_id;
        }

        if ($organizationId === null) {
            $organizationId = required_payload_int(
                $payload,
                'organization_id',
                'organization_id is required when product_group_id is not provided.'
            );
        }

        if (! empty($payload['product_group_id'])) {
            $this->assertProductGroupBelongsToOrganization((int) $payload['product_group_id'], $organizationId);
        }

        return Product::query()->create([
            'item_id' => $payload['item_id'],
            'code' => $payload['code'] ?? null,
            'barcode' => $payload['barcode'] ?? null,
            'organization_id' => $organizationId,
            'product_group_id' => $payload['product_group_id'] ?? null,
            'default_measurement_id' => $payload['default_measurement_id'] ?? null,
            'track_stock' => $payload['track_stock'] ?? true,
            'track_batch' => $payload['track_batch'] ?? false,
            'track_expiry' => $payload['track_expiry'] ?? false,
        ])->load(['item:id,name,item_type', 'productGroup:id,name', 'defaultMeasurement:id,name,symbol']);
    }

    public function find(User $actor, int $productId): Product
    {
        return Product::query()
            ->with(['item:id,name,item_type', 'productGroup:id,name', 'defaultMeasurement:id,name,symbol'])
            ->findOrFail($productId);
    }

    public function update(User $actor, int $productId, array $payload): Product
    {
        $product = Product::query()->findOrFail($productId);
        $organizationId = (int) $product->organization_id;

        if (array_key_exists('product_group_id', $payload) && ! empty($payload['product_group_id'])) {
            $this->assertProductGroupBelongsToOrganization((int) $payload['product_group_id'], $organizationId);
        }

        $product->update([
            'item_id' => $payload['item_id'] ?? $product->item_id,
            'code' => array_key_exists('code', $payload) ? $payload['code'] : $product->code,
            'barcode' => array_key_exists('barcode', $payload) ? $payload['barcode'] : $product->barcode,
            'product_group_id' => array_key_exists('product_group_id', $payload) ? $payload['product_group_id'] : $product->product_group_id,
            'default_measurement_id' => array_key_exists('default_measurement_id', $payload) ? $payload['default_measurement_id'] : $product->default_measurement_id,
            'track_stock' => array_key_exists('track_stock', $payload) ? $payload['track_stock'] : $product->track_stock,
            'track_batch' => array_key_exists('track_batch', $payload) ? $payload['track_batch'] : $product->track_batch,
            'track_expiry' => array_key_exists('track_expiry', $payload) ? $payload['track_expiry'] : $product->track_expiry,
        ]);

        return $product->load(['item:id,name,item_type', 'productGroup:id,name', 'defaultMeasurement:id,name,symbol']);
    }

    public function delete(User $actor, int $productId): void
    {
        $product = Product::query()->findOrFail($productId);
        $product->delete();
    }

    private function assertProductGroupBelongsToOrganization(int $productGroupId, int $organizationId): void
    {
        $exists = ProductGroup::query()
            ->where('id', $productGroupId)
            ->where('organization_id', $organizationId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'product_group_id' => ['The selected product group does not belong to this organization.'],
            ]);
        }
    }
}
