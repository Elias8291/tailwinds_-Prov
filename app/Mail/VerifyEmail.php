<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;

    /** Constructor para inicializar datos del correo */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->verificationUrl = route('verification.verify', [
            'id' => $user->id,
            'token' => $user->verification_token
        ]);
    }

    /** Construir el mensaje de correo */
    public function build()
    {
        return $this->subject('Verificación de Cuenta - Padrón de Proveedores')
                    ->view('emails.verify-email')
                    ->with([
                        'user' => $this->user,
                        'verificationUrl' => $this->verificationUrl,
                        'expirationHours' => 72
                    ]);
    }
} 