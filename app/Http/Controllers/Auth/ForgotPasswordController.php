<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Find user by correo field
        $user = \App\Models\User::where('correo', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'No pudimos encontrar un usuario con ese correo electrónico.']);
        }

        // Generate token
        $token = app('auth.password.tokens')->create($user);
        
        // Send email manually
        \Illuminate\Support\Facades\Mail::to($user->correo)->send(new \App\Mail\ResetPassword($token, $user->correo));

        return back()->with('status', 'Te hemos enviado un correo con las instrucciones para restablecer tu contraseña.');
    }
} 