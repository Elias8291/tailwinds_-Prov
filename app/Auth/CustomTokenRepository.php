<?php

namespace App\Auth;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\CanResetPassword;
use Carbon\Carbon;

class CustomTokenRepository
{
    protected $table;
    protected $expire;

    public function __construct($table = 'password_reset_tokens', $expire = 60)
    {
        $this->table = $table;
        $this->expire = $expire;
    }

    /**
     * Create a new token for the user.
     */
    public function create(CanResetPassword $user)
    {
        $this->deleteExisting($user);
        
        $token = $this->createNewToken();
        
        DB::table($this->table)->insert([
            'correo' => $user->getEmailForPasswordReset(),
            'token' => Hash::make($token),
            'created_at' => new Carbon,
        ]);
        
        return $token;
    }

    /**
     * Determine if a token record exists and is valid.
     */
    public function exists(CanResetPassword $user, $token)
    {
        $record = DB::table($this->table)
            ->where('correo', $user->getEmailForPasswordReset())
            ->first();

        return $record &&
               !$this->tokenExpired($record->created_at) &&
               Hash::check($token, $record->token);
    }

    /**
     * Delete a token record.
     */
    public function delete(CanResetPassword $user)
    {
        $this->deleteExisting($user);
    }

    /**
     * Delete all existing reset tokens from the database.
     */
    protected function deleteExisting(CanResetPassword $user)
    {
        return DB::table($this->table)
            ->where('correo', $user->getEmailForPasswordReset())
            ->delete();
    }

    /**
     * Determine if the token has expired.
     */
    protected function tokenExpired($createdAt)
    {
        return Carbon::parse($createdAt)->addMinutes($this->expire)->isPast();
    }

    /**
     * Create a new token.
     */
    protected function createNewToken()
    {
        return hash_hmac('sha256', Str::random(40), config('app.key'));
    }

    /**
     * Find user by correo and token, then validate.
     */
    public function findUserByToken($correo, $token)
    {
        $user = \App\Models\User::where('correo', $correo)->first();
        
        if (!$user) {
            return null;
        }

        if (!$this->exists($user, $token)) {
            return null;
        }

        return $user;
    }
} 