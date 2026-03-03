<?php

namespace App\Services\User;
use App\Models\User;

class UserService
{

        public function RegisterUser(array $payload) : array {

            $user = User::create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? null,
                'national_id' => $payload['national_id'] ?? null,
                'password' => $payload['password'],
            ]);
    
            return [
                'user' => $user,
            ];
    
        }
}
