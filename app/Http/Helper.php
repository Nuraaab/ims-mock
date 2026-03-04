<?php

use App\Models\User;
use App\Services\Auth\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

if (! function_exists('api_success')) {
    /**
     * Standard success response helper.
     */
    function api_success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if (! empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }
}

if (! function_exists('api_error')) {
    /**
     * Standard error response helper.
     */
    function api_error(
        string $message = 'Something went wrong.',
        int $status = 400,
        array $errors = []
    ): JsonResponse {
        $payload = [
            'message' => $message,
        ];

        if (! empty($errors)) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}

if (! function_exists('auth_user')) {
    /**
     * Current authenticated user helper.
     */
    function auth_user(): mixed
    {
        return Auth::user();
    }
}

if (! function_exists('permission_service')) {
    /**
     * Resolve permission service from container.
     */
    function permission_service(): PermissionService
    {
        return app(PermissionService::class);
    }
}

if (! function_exists('scope_ids')) {
    /**
     * Get accessible scope IDs for a user + permission key.
     */
    function scope_ids(User $user, string $permissionKey, string $scope): array
    {
        return permission_service()->getAccessibleScopeIds($user, $permissionKey, $scope);
    }
}

if (! function_exists('require_scope_ids')) {
    /**
     * Get accessible scope IDs or throw a standardized validation error.
     */
    function require_scope_ids(
        User $user,
        string $permissionKey,
        string $scope,
        string $field = 'organization_id',
        ?string $message = null
    ): array {
        $ids = scope_ids($user, $permissionKey, $scope);

        if (empty($ids)) {
            throw ValidationException::withMessages([
                $field => [$message ?? "No accessible {$scope} scope found for the current user."],
            ]);
        }

        return $ids;
    }
}

if (! function_exists('required_payload_int')) {
    /**
     * Ensure a payload key exists and is not null/empty, then cast to int.
     */
    function required_payload_int(array $payload, string $key, string $message): int
    {
        if (! array_key_exists($key, $payload) || $payload[$key] === null || $payload[$key] === '') {
            throw ValidationException::withMessages([
                $key => [$message],
            ]);
        }

        return (int) $payload[$key];
    }
}
