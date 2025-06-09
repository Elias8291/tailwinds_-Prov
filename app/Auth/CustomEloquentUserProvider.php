<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomEloquentUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            str_contains(strtolower(key($credentials)), 'password'))) {
            return;
        }

        // Convert email to correo if present
        if (isset($credentials['email'])) {
            $credentials['correo'] = $credentials['email'];
            unset($credentials['email']);
        }

        // Build the query for the given credentials.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (str_contains(strtolower($key), 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof \ArrayAccess) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        // Convert email to correo if present for validation
        if (isset($credentials['email'])) {
            $credentials['correo'] = $credentials['email'];
            unset($credentials['email']);
        }

        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
} 