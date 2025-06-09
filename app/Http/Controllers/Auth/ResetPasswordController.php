<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'token.required' => 'Token de recuperación requerido.',
        ]);

        $tokenRepository = app('auth.password.tokens');
        
        // Find user by correo and validate token
        $user = $tokenRepository->findUserByToken($request->email, $request->token);
        
        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'El enlace de recuperación es inválido o ha expirado.']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Delete the token
        $tokenRepository->delete($user);
        
        // Log the user in
        \Illuminate\Support\Facades\Auth::login($user);

        return redirect($this->redirectPath())
            ->with('status', 'Tu contraseña ha sido restablecida exitosamente.');
    }
} 