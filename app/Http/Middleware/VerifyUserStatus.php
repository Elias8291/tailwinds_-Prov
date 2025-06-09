<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyUserStatus
{
    /** Handle an incoming request */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->estado !== 'activo') {
            Auth::logout();
            
            return redirect()->route('login')->with('error', 
                'Tu cuenta no está verificada. Por favor, verifica tu correo electrónico antes de continuar.');
        }

        return $next($request);
    }
} 