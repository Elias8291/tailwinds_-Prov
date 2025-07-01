<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // Log para debugging (solo en desarrollo)
                if (config('app.debug')) {
                    Log::info('Usuario autenticado intentando acceder a ruta guest: ' . $request->url());
                }
                
                // Si el usuario ya está autenticado, redirigir al dashboard
                // Verificar si ya estamos en el dashboard para evitar loops
                if ($request->is('dashboard') || $request->is('dashboard/*')) {
                    return $next($request);
                }
                
                // Redirigir específicamente según la ruta solicitada
                if ($request->is('/') || $request->is('welcome')) {
                    return redirect()->route('dashboard')->with('info', 'Ya tienes una sesión activa.');
                }
                
                if ($request->is('iniciar-sesion') || $request->is('login')) {
                    return redirect()->route('dashboard')->with('info', 'Ya tienes una sesión activa.');
                }
                
                if ($request->is('registro') || $request->is('register')) {
                    return redirect()->route('dashboard')->with('info', 'Ya tienes una sesión activa.');
                }
                
                // Para cualquier otra ruta guest, redirigir al HOME por defecto
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
} 