<?php

namespace App\Services\Auth;

use App\Models\AppPermission;
use App\Models\AppRole;
use App\Models\Kebele;
use App\Models\Locality;
use App\Models\User;
use App\Models\UserRoleBinding;
use App\Models\Woreda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Modules\IMS\Models\Organization;
use Modules\IMS\Models\OrganizationHistory;
use Modules\IMS\Models\TaxCenter;

class AuthService
{
    public function getUserPermissionKeys(User $user): array
    {
        return UserRoleBinding::query()
            ->where('user_id', $user->id)
            ->with('role.permissions:id,key')
            ->get()
            ->pluck('role.permissions')
            ->flatten(1)
            ->pluck('key')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function getRegistrationLookups(): array
    {
        return [
            'woredas' => Woreda::query()
                ->select(['id', 'name'])
                ->orderBy('name')
                ->get(),
            'kebeles' => Kebele::query()
                ->select(['id', 'name', 'woreda_id'])
                ->orderBy('name')
                ->get(),
            'localities' => Locality::query()
                ->select(['id', 'name', 'kebele_id'])
                ->orderBy('name')
                ->get(),
            'tax_centers' => TaxCenter::query()
                ->select(['id', 'name', 'woreda_id'])
                ->orderBy('name')
                ->get(),
        ];
    }

    public function registerOrganization(array $payload): array
    {
        return DB::transaction(function () use ($payload) {
            $userData = $payload['user'];
            $organizationData = $payload['organization'];

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'phone' => $userData['phone'] ?? null,
                'national_id' => $userData['national_id'] ?? null,
                'password' => $userData['password'],
            ]);

            $organization = Organization::create($organizationData);

            OrganizationHistory::create([
                'organization_id' => $organization->id,
                'name' => $organization->name,
                'tin_number' => $organization->tin_number,
                'VAT_reg_number' => $organization->VAT_reg_number,
                'VAT_reg_date' => $organization->VAT_reg_date,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'house_number' => $organization->house_number,
                'trade_name' => $organization->trade_name,
                'legal_name' => $organization->legal_name,
                'woreda_id' => $organization->woreda_id,
                'kebele_id' => $organization->kebele_id,
                'locality_id' => $organization->locality_id,
                'tax_center_id' => $organization->tax_center_id,
                'sector_type' => $organization->sector_type,
            ]);

            $superOwnerRole = AppRole::firstOrCreate(['name' => 'Super Owner']);
            $superOwnerRole->permissions()->sync(
                AppPermission::query()->pluck('id')->all()
            );

            UserRoleBinding::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'role_id' => $superOwnerRole->id,
                    'scope' => 'organization',
                    'scope_id' => $organization->id,
                ],
                ['include_descendents' => true]
            );

            $token = $user->createToken('auth_token')->plainTextToken;

            return compact('user', 'organization', 'token');
        });
    }

    public function login(array $credentials): array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name'] ?? 'auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
