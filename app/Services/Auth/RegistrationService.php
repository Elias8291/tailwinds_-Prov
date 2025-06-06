<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Solicitante;
use App\Models\Direccion;
use App\Models\Documento;
use App\Models\DocumentoSolicitante;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use App\Mail\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class RegistrationService
{
    public function secureData(array $data): string
    {
        Log::info('Procesando datos seguros para registro');
        return Str::uuid()->toString();
    }

    public function register(array $data, UploadedFile $satFile, string $secureDataToken): User
    {
        return DB::transaction(function () use ($data, $satFile, $secureDataToken) {
            $secureData = session('secure_registration_' . $secureDataToken);
            if (!$secureData) {
                throw new \Exception('Error de seguridad: datos no encontrados o expirados.');
            }

            // Verificar usuario existente
            $existingUser = User::where('rfc', $secureData['rfc'])
                ->orWhere('correo', $data['email'])
                ->first();

            if ($existingUser && $existingUser->fecha_verificacion_correo) {
                throw new \Exception('El RFC o correo electrónico ya está registrado y verificado.');
            }

            $verificationToken = Str::random(64);

            if ($existingUser) {
                $user = $this->updateExistingUser($existingUser, $secureData, $data, $verificationToken);
            } else {
                $user = $this->createNewUser($secureData, $data, $verificationToken);
            }

            // Procesar documento SAT
            $this->processDocument($satFile, $user->solicitante->tramite);

            // Enviar correo de verificación
            $this->sendVerificationEmail($user, $verificationToken);

            // Limpiar datos de sesión
            session()->forget('secure_registration_' . $secureDataToken);
            session()->forget('temp_sat_file_name');
            session()->forget('temp_sat_file_path');

            return $user;
        });
    }

    protected function updateExistingUser(User $user, array $secureData, array $data, string $verificationToken): User
    {
        $user->update([
            'nombre' => $secureData['nombre'],
            'correo' => $data['email'],
            'rfc' => $secureData['rfc'],
            'password' => Hash::make($data['password']),
            'verification_token' => $verificationToken,
        ]);

        $this->updateOrCreateSolicitante($user, $secureData);
        return $user;
    }

    protected function createNewUser(array $secureData, array $data, string $verificationToken): User
    {
        $user = User::create([
            'nombre' => $secureData['nombre'],
            'correo' => $data['email'],
            'rfc' => $secureData['rfc'],
            'password' => Hash::make($data['password']),
            'estado' => 'pendiente',
            'verification_token' => $verificationToken,
        ]);

        $this->updateOrCreateSolicitante($user, $secureData);
        return $user;
    }

    protected function updateOrCreateSolicitante(User $user, array $secureData): void
    {
        $solicitante = $user->solicitante ?? new Solicitante();
        $solicitante->fill([
            'usuario_id' => $user->id,
            'tipo_persona' => $secureData['tipo_persona'],
            'curp' => $secureData['curp'],
            'rfc' => $secureData['rfc'],
        ]);
        $user->solicitante()->save($solicitante);

        $direccion = Direccion::create([
            'codigo_postal' => $secureData['cp'],
            'asentamiento_id' => null,
            'calle' => $secureData['direccion'],
        ]);

        $tramite = $solicitante->tramites()->firstOrCreate(
            ['tipo_tramite' => 'Inscripcion'],
            [
                'estado' => 'Pendiente',
                'progreso_tramite' => 0,
                'fecha_inicio' => now(),
            ]
        );

        DetalleTramite::updateOrCreate(
            ['tramite_id' => $tramite->id],
            [
                'razon_social' => $secureData['nombre'],
                'email' => $user->correo,
                'direccion_id' => $direccion->id,
            ]
        );
    }

    protected function processDocument(UploadedFile $file, Tramite $tramite): void
    {
        $documento = Documento::where('nombre', 'Constancia de Situación Fiscal')->firstOrFail();
        
        $nombreArchivo = uniqid('doc_' . $documento->id . '_') . '.pdf';
        $ruta = $file->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');
        $rutaEncriptada = Crypt::encryptString($ruta);

        DocumentoSolicitante::updateOrCreate(
            [
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
            ],
            [
                'fecha_entrega' => now(),
                'estado' => 'Aprobado',
                'version_documento' => DB::raw('COALESCE(version_documento, 0) + 1'),
                'ruta_archivo' => $rutaEncriptada,
            ]
        );
    }

    protected function sendVerificationEmail(User $user, string $verificationToken): void
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verify.email',
            now()->addHours(72),
            ['user_id' => $user->id, 'token' => $verificationToken]
        );

        Mail::to($user->correo)->send(new VerifyEmail($user, $verificationUrl));
    }

    public function verifyEmail(int $userId, string $token): void
    {
        $user = User::findOrFail($userId);

        if ($user->fecha_verificacion_correo) {
            throw new \Exception('Tu correo ya está verificado.');
        }

        if (!$token || $user->verification_token !== $token) {
            throw new \Exception('El token de verificación no es válido.');
        }

        DB::transaction(function () use ($user) {
            $user->update([
                'estado' => 'activo',
                'fecha_verificacion_correo' => now(),
                'verification_token' => null,
            ]);

            $user->assignRole('solicitante');
        });
    }
} 