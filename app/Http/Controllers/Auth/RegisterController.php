<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DireccionController;
use App\Http\Controllers\SolicitanteController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DetalleTramiteController;
use App\Http\Controllers\DocumentoController;
use App\Mail\VerifyEmail;
use App\Jobs\DeleteUnverifiedUsers;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Solicitante;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Models\Direccion;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /** Show registration form */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /** Handle user registration with email verification and resend logic */
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->validateRegistrationData($request->all());
            $satData = $this->extractSatData($request->qr_url);
            
            // Verificar si el usuario ya existe pero no está verificado
            $existingUser = User::where('correo', $request->email)->where('estado', 'pendiente')->first();
            
            if ($existingUser) {
                // Actualizar datos existentes y reenviar verificación
                $user = $this->updateExistingUser($existingUser, $request, $satData);
                $isResend = true;
            } else {
                // Crear nuevo usuario
                $user = $this->createCompleteRegistration($request, $satData);
                $isResend = false;
            }

            // Enviar correo de verificación
            Mail::to($user->correo)->send(new VerifyEmail($user));
            
            // Programar eliminación automática en 72 horas
            DeleteUnverifiedUsers::dispatch($user->id)->delay(now()->addHours(72));

            DB::commit();
            Log::info('Registro completado exitosamente', ['user_id' => $user->id, 'is_resend' => $isResend]);

            $message = $isResend 
                ? 'Se ha actualizado tu información y reenviado el correo de verificación. Revisa tu bandeja de entrada.'
                : 'Registro completado exitosamente. Se ha enviado un correo de verificación a tu dirección de email. Tienes 72 horas para verificar tu cuenta.';

            return redirect()->route('login')->with('registration_success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error en el registro', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->withErrors(['registration' => 'Error en el registro: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /** Validate all registration data using respective controllers */
    protected function validateRegistrationData(array $data)
    {
        $userController = new UserController();
        $solicitanteController = new SolicitanteController();
        $documentoController = new DocumentoController();

        $userController->validateRegistrationData($data);
        $solicitanteController->validateSatData($data);
        $documentoController->validateRegistrationDocument($data);
    }

    /** Extract SAT data from frontend processed request */
    protected function extractSatData($qrUrl)
    {
        $request = request();
        
        return [
            'rfc' => $request->sat_rfc ?: $this->extractRfcFromUrl($qrUrl),
            'nombre' => $request->sat_nombre ?: 'Sin nombre',
            'tipo_persona' => $request->sat_tipo_persona ?: 'Física',
            'curp' => $request->sat_curp,
            'cp' => $request->sat_cp ?: '00000',
            'colonia' => $request->sat_colonia,
            'nombreVialidad' => $request->sat_nombre_vialidad ?: 'Sin dirección',
            'numeroExterior' => $request->sat_numero_exterior,
            'numeroInterior' => $request->sat_numero_interior,
            'email' => null,
        ];
    }

    /** Extract RFC from SAT URL or generate test RFC */
    protected function extractRfcFromUrl($url)
    {
        if (empty($url)) {
            return 'TEST' . date('ymd') . Str::random(3);
        }
        
        if (preg_match('/rfc=([A-Z0-9]+)/i', $url, $matches)) {
            return $matches[1];
        }
        
        return 'TEST' . date('ymd') . Str::random(3);
    }

    /** Create complete user registration using specialized controllers */
    protected function createCompleteRegistration(Request $request, array $satData)
    {
        $userController = new UserController();
        $direccionController = new DireccionController();
        $solicitanteController = new SolicitanteController();
        $tramiteController = new TramiteController();
        $detalleTramiteController = new DetalleTramiteController();
        $documentoController = new DocumentoController();

        $direccion = $direccionController->create([
            'codigo_postal' => $satData['cp'],
            'calle' => $satData['nombreVialidad'],
            'numero_exterior' => $satData['numeroExterior'] ?? null,
            'numero_interior' => $satData['numeroInterior'] ?? null,
        ]);

        $user = $userController->createForRegistration([
            'nombre' => $satData['nombre'],
            'correo' => $request->email,
            'rfc' => $satData['rfc'],
            'password' => $request->password,
            'estado' => 'pendiente',
        ]);

        // Asignar rol de solicitante al usuario
        $this->assignSolicitanteRole($user);

        // Agregar razón social igual al nombre del usuario
        $satData['razon_social'] = $satData['nombre'];
        $satData['nombre_completo'] = $satData['nombre'];
        
        $solicitante = $solicitanteController->create($user, $satData);

        $tramite = $tramiteController->createForRegistration($solicitante, [
            'tipo_tramite' => 'Inscripcion',
            'estado' => 'Pendiente',
            'progreso_tramite' => 0,
            'fecha_inicio' => now(),
        ]);

        $detalleTramiteController->create($tramite, [
            'razon_social' => $satData['nombre'],
            'email' => $request->email,
            'direccion_id' => $direccion->id,
        ]);

        $documentoController->processConstanciaDocument($request->file('document'), $tramite);

        return $user;
    }

    /** Update existing unverified user with new data */
    protected function updateExistingUser(User $existingUser, Request $request, array $satData)
    {
        $userController = new UserController();
        $direccionController = new DireccionController();
        $solicitanteController = new SolicitanteController();
        $tramiteController = new TramiteController();
        $detalleTramiteController = new DetalleTramiteController();
        $documentoController = new DocumentoController();

        // Actualizar token de verificación
        $existingUser->update([
            'verification_token' => Str::random(64),
            'nombre' => $satData['nombre'],
            'rfc' => $satData['rfc']
        ]);

        // Asignar rol de solicitante si no lo tiene
        $this->assignSolicitanteRole($existingUser);

        // Buscar datos relacionados existentes
        $solicitante = $existingUser->solicitantes()->first();
        $tramite = $solicitante ? $solicitante->tramites()->first() : null;
        $detalleTramite = $tramite ? $tramite->detalleTramite()->first() : null;
        $direccion = $detalleTramite ? $detalleTramite->direccion : null;

        // Actualizar direccion existente o crear nueva
        if ($direccion) {
            $direccionController->update($direccion, [
                'codigo_postal' => $satData['cp'],
                'calle' => $satData['nombreVialidad'],
                'numero_exterior' => $satData['numeroExterior'] ?? null,
                'numero_interior' => $satData['numeroInterior'] ?? null,
            ]);
        } else {
            $direccion = $direccionController->create([
                'codigo_postal' => $satData['cp'],
                'calle' => $satData['nombreVialidad'],
                'numero_exterior' => $satData['numeroExterior'] ?? null,
                'numero_interior' => $satData['numeroInterior'] ?? null,
            ]);
        }

        // Actualizar solicitante existente o crear nuevo
        if ($solicitante) {
            $solicitanteController->update($solicitante, [
                'tipo_persona' => $satData['tipo_persona'],
                'curp' => $satData['tipo_persona'] === 'Física' ? ($satData['curp'] ?? null) : null,
                'rfc' => $satData['rfc'],
                'razon_social' => $satData['nombre'],
                'nombre_completo' => $satData['nombre'],
            ]);
        } else {
            // Agregar razón social igual al nombre del usuario
            $satData['razon_social'] = $satData['nombre'];
            $satData['nombre_completo'] = $satData['nombre'];
            $solicitante = $solicitanteController->create($existingUser, $satData);
        }

        // Actualizar detalle de trámite existente o crear nuevo
        if ($detalleTramite) {
            $detalleTramiteController->update($detalleTramite, [
                'razon_social' => $satData['nombre'],
                'email' => $request->email,
                'direccion_id' => $direccion->id,
            ]);
        } else {
            if (!$tramite) {
                $tramite = $tramiteController->createForRegistration($solicitante, [
                    'tipo_tramite' => 'Inscripcion',
                    'estado' => 'Pendiente',
                    'progreso_tramite' => 0,
                    'fecha_inicio' => now(),
                ]);
            }
            
            $detalleTramiteController->create($tramite, [
                'razon_social' => $satData['nombre'],
                'email' => $request->email,
                'direccion_id' => $direccion->id,
            ]);
        }

        // Procesar nuevo documento
        if ($tramite) {
            $documentoController->processConstanciaDocument($request->file('document'), $tramite);
        }

        Log::info('Usuario existente actualizado', ['user_id' => $existingUser->id]);
        return $existingUser;
    }

    /** Asignar rol de solicitante al usuario */
    protected function assignSolicitanteRole(User $user)
    {
        try {
            // Verificar si el rol 'solicitante' existe, si no, crearlo
            $solicitanteRole = Role::firstOrCreate(['name' => 'solicitante']);
            
            // Asignar el rol si el usuario no lo tiene
            if (!$user->hasRole('solicitante')) {
                $user->assignRole($solicitanteRole);
                Log::info('Rol solicitante asignado', ['user_id' => $user->id]);
            }
        } catch (\Exception $e) {
            Log::warning('Error al asignar rol solicitante', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

}
