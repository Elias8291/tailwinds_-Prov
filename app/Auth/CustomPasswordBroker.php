<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Closure;
use Illuminate\Support\Arr;

class CustomPasswordBroker extends PasswordBroker
{
    /**
     * Send a password reset link to a user.
     *
     * @param  array  $credentials
     * @param  \Closure|null  $callback
     * @return string
     */
    public function sendResetLink(array $credentials, Closure $callback = null)
    {
        // Change 'email' to 'correo' for our custom field
        if (isset($credentials['email'])) {
            $credentials['correo'] = $credentials['email'];
            unset($credentials['email']);
        }

        // Retrieve the user by correo field
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = $this->createToken($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            $user->sendPasswordResetNotification($token);
        }

        return static::RESET_LINK_SENT;
    }

    /**
     * Reset the password for the given token.
     *
     * @param  array  $credentials
     * @param  \Closure  $callback
     * @return mixed
     */
    public function reset(array $credentials, Closure $callback)
    {
        // Change 'email' to 'correo' for our custom field
        if (isset($credentials['email'])) {
            $credentials['correo'] = $credentials['email'];
            unset($credentials['email']);
        }

        $user = $this->validateReset($credentials);

        if (! $user instanceof CanResetPasswordContract) {
            return $user;
        }

        $password = $credentials['password'];

        $callback($user, $password);

        $this->deleteToken($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|null
     */
    public function getUser(array $credentials)
    {
        $credentials = Arr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && ! $user instanceof CanResetPasswordContract) {
            throw new \UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\CanResetPassword|string
     */
    protected function validateReset(array $credentials)
    {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (! $this->validateToken($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Validate the given password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @param  string  $token
     * @return bool
     */
    public function validateToken(CanResetPasswordContract $user, $token)
    {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return string
     */
    public function createToken(CanResetPasswordContract $user)
    {
        return $this->tokens->create($user);
    }

    /**
     * Delete password reset tokens of the given user.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return void
     */
    public function deleteToken(CanResetPasswordContract $user)
    {
        $this->tokens->delete($user);
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword  $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        return $this->tokens->recentlyCreatedToken($user);
    }
} 