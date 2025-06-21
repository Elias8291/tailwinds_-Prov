<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\SystemLogService;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        login as protected traitLogin;
        logout as protected traitLogout;
    }

    // protected $redirectTo = RouteServiceProvider::HOME; // Removido para usar authenticated() method

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function username()
    {
        return 'rfc';
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ], [
            'rfc.required' => 'El RFC es obligatorio',
            'password.required' => 'La contraseña es obligatoria',
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return redirect()
            ->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->with('error', 'Las credenciales proporcionadas no coinciden con nuestros registros.');
    }

    protected function authenticated(Request $request, $user)
    {
        $user->ultimo_acceso = now();
        $user->save();

        // Inicializar la última actividad para el middleware de expiración de sesión
        session(['last_activity' => now()]);

        // Log del login exitoso
        SystemLogService::userLogin($user->correo);

        // Log para debugging
        Log::info('Usuario autenticado', [
            'user_id' => $user->id,
            'user_name' => $user->nombre,
            'user_roles' => $user->roles->pluck('name')->toArray(),
            'user_permissions' => $user->getAllPermissions()->pluck('name')->toArray()
        ]);

        // Redirigir al dashboard
        Log::info('Redirigiendo a dashboard');
        return redirect()->intended(route('dashboard'));
    }

    /** Handle user login with email verification check */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $rfc = $request->input($this->username());
        $password = $request->input('password');
        
        // Buscar usuario por RFC
        $user = User::where('rfc', $rfc)->first();
        
        if (!$user) {
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->with('error', 'No se encontró una cuenta con este RFC.');
        }

        // Verificar contraseña
        if (!Hash::check($password, $user->password)) {
            // Log del intento fallido
            SystemLogService::loginFailed($user->correo);
            
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->with('error', 'La contraseña es incorrecta.');
        }

        // Verificar si la cuenta está verificada
        if ($user->estado !== 'activo') {
            // Verificar si han pasado 72 horas para eliminar cuenta
            if ($user->created_at->diffInHours(now()) > 72) {
                $this->deleteExpiredUser($user);
                return redirect()->route('register')
                    ->with('error', 'Tu cuenta ha expirado y ha sido eliminada. Por favor, regístrate nuevamente.');
            }

            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->with('verification_required', [
                    'message' => 'Tu cuenta no ha sido verificada. Revisa tu correo electrónico y haz clic en el enlace de verificación.',
                    'email' => $user->correo,
                    'hours_left' => 72 - $user->created_at->diffInHours(now())
                ]);
        }

        // Realizar login normal si todo está correcto
        $credentials = $request->only($this->username(), 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            Log::info('Login exitoso, procesando redirección', [
                'user_id' => Auth::user()->id,
                'intended_url' => session('url.intended', 'no_intended')
            ]);
            
            return $this->authenticated($request, Auth::user());
        }

        return $this->sendFailedLoginResponse($request);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        
        // Log del logout
        if ($user) {
            SystemLogService::userLogout($user->correo);
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /** Delete expired user and related data */
    private function deleteExpiredUser($user)
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
        
        \Illuminate\Support\Facades\Log::info('Usuario expirado eliminado desde login', [
            'user_id' => $user->id,
            'email' => $user->correo
        ]);
    }
} 