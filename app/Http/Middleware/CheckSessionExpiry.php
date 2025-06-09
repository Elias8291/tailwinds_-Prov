<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Solo verificar si el usuario está autenticado
        if (Auth::check()) {
            $sessionLifetime = config('session.lifetime', 30); // minutos
            $lastActivity = Session::get('last_activity');
            
            if ($lastActivity) {
                $timeSinceLastActivity = now()->diffInMinutes($lastActivity);
                
                // Si han pasado más minutos que el límite de sesión
                if ($timeSinceLastActivity > $sessionLifetime) {
                    Auth::logout();
                    Session::invalidate();
                    Session::regenerateToken();
                    
                    return redirect()->route('login')
                        ->with('session_expired', 'Tu sesión ha expirado por inactividad. Por favor, inicia sesión nuevamente.');
                }
            }
            
            // Actualizar la hora de última actividad
            Session::put('last_activity', now());
        }

        return $next($request);
    }
}
