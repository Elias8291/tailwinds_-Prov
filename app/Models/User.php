<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
  protected $fillable = [
        'nombre',
        'correo',
        'rfc',
        'password',
        'estado',
        'verification_token',
        'fecha_verificacion_correo',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'fecha_verificacion_correo' => 'datetime',
        'ultimo_acceso' => 'datetime',
        'password' => 'hashed',
    ];

    /** Get the solicitante record associated with the user */
    public function solicitante()
    {
        return $this->hasOne(Solicitante::class, 'usuario_id');
    }

    /** Get all solicitantes records associated with the user */
    public function solicitantes()
    {
        return $this->hasMany(Solicitante::class, 'usuario_id');
    }

    public function getNameAttribute()
    {
        return $this->nombre;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        Mail::to($this->correo)->send(new ResetPassword($token, $this->correo));
    }

    /**
     * Get the email address for password resets.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    /**
     * Get the email attribute (maps to correo for compatibility)
     * 
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->correo;
    }
}