<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerificationController extends Controller
{
    /** Verificar email con token */
    public function verify(Request $request, $id, $token)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('login')->with('error', 
                'El enlace de verificación no es válido.');
        }

        if ($user->estado === 'activo') {
            return redirect()->route('login')->with('info', 
                'Tu cuenta ya ha sido verificada. Puedes iniciar sesión.');
        }

        if ($user->verification_token !== $token) {
            return redirect()->route('login')->with('error', 
                'El enlace de verificación no es válido o ha expirado.');
        }

        // Verificar si han pasado más de 72 horas
        if ($user->created_at->diffInHours(now()) > 72) {
            Log::info('Token de verificación expirado', ['user_id' => $user->id]);
            
            // Eliminar usuario y datos relacionados
            $this->deleteUserData($user);
            
            return redirect()->route('register')->with('error', 
                'El enlace de verificación ha expirado. Tu cuenta ha sido eliminada. Por favor, regístrate nuevamente.');
        }

        // Activar usuario
        $user->update([
            'estado' => 'activo',
            'email_verified_at' => now(),
            'verification_token' => null
        ]);

        Log::info('Usuario verificado exitosamente', ['user_id' => $user->id]);

        return redirect()->route('login')->with('verification_success', 
            '¡Cuenta verificada exitosamente! Ya puedes iniciar sesión.');
    }

    /** Reenviar correo de verificación */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,correo'
        ]);

        $user = User::where('correo', $request->email)
                   ->where('estado', 'pendiente')
                   ->first();

        if (!$user) {
            return back()->with('error', 
                'No se encontró una cuenta pendiente de verificación con este correo.');
        }

        // Verificar si han pasado más de 72 horas
        if ($user->created_at->diffInHours(now()) > 72) {
            $this->deleteUserData($user);
            return back()->with('error', 
                'Tu cuenta ha expirado y ha sido eliminada. Por favor, regístrate nuevamente.');
        }

        // Generar nuevo token y enviar correo
        $user->update(['verification_token' => \Illuminate\Support\Str::random(64)]);
        \Illuminate\Support\Facades\Mail::to($user->correo)->send(new \App\Mail\VerifyEmail($user));

        Log::info('Correo de verificación reenviado', ['user_id' => $user->id]);

        return back()->with('success', 
            'Correo de verificación reenviado exitosamente.');
    }

    /** Eliminar datos del usuario en cascada */
    private function deleteUserData($user)
    {
        if ($user->solicitantes) {
            foreach ($user->solicitantes as $solicitante) {
                if ($solicitante->tramites) {
                    foreach ($solicitante->tramites as $tramite) {
                        $tramite->documentosSolicitante()->delete();
                        $tramite->detalleTramite()->delete();
                        $tramite->delete();
                    }
                }
                $solicitante->delete();
            }
        }
        $user->delete();
    }
} 