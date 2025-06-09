<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $email;

    /**
     * Create a new message instance.
     *
     * @param string $token
     * @param string $email
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@oaxaca.gob.mx', 'Gobierno de Oaxaca')
                    ->subject('Restablecer Contraseña - Padrón de Proveedores')
                    ->view('emails.reset-password')
                    ->with([
                        'resetUrl' => url(route('password.reset', [
                            'token' => $this->token,
                            'email' => $this->email,
                        ], false)),
                        'userEmail' => $this->email,
                    ]);
    }
} 