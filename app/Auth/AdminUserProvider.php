<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Support\Arrayable;

class AdminUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     * Override to handle email-based lookup for password reset
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (
            empty($credentials) ||
            (count($credentials) === 1 && array_key_exists('password', $credentials))
        ) {
            return;
        }

        // For password reset, Laravel passes ['email' => 'user@example.com']
        // We need to find admin by email for password reset
        if (array_key_exists('email', $credentials) && count($credentials) === 1) {
            return $this->newModelQuery()->where('email', $credentials['email'])->first();
        }

        // For regular authentication, use the default behavior
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }
}
