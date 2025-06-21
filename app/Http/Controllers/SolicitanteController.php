<?php

namespace App\Http\Controllers;

use App\Models\Solicitante;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolicitanteController extends Controller
{
    /** Create solicitante from user and SAT data */
    public function create(User $user, array $satData): Solicitante
    {
        try {
            $solicitante = Solicitante::create([
                'usuario_id' => $user->id,
                'tipo_persona' => $satData['tipo_persona'],
                'curp' => $satData['tipo_persona'] === 'Física' ? ($satData['curp'] ?? null) : null,
                'rfc' => $satData['rfc'],
                'razon_social' => $satData['razon_social'] ?? $satData['nombre'] ?? null,
                'nombre_completo' => $satData['nombre_completo'] ?? $satData['nombre'] ?? null,
            ]);

            Log::info('Solicitante creado exitosamente', [
                'solicitante_id' => $solicitante->id,
                'razon_social' => $solicitante->razon_social,
                'nombre_completo' => $solicitante->nombre_completo
            ]);
            return $solicitante;

        } catch (\Exception $e) {
            Log::error('Error al crear solicitante', ['error' => $e->getMessage()]);
            throw new \Exception('Error al crear el solicitante: ' . $e->getMessage());
        }
    }

    /**
     * Update solicitante record
     *
     * @param Solicitante $solicitante
     * @param array $data
     * @return Solicitante
     */
    public function update(Solicitante $solicitante, array $data): Solicitante
    {
        try {
            $solicitante->update($data);

            Log::info('Solicitante actualizado exitosamente', [
                'solicitante_id' => $solicitante->id
            ]);

            return $solicitante;

        } catch (\Exception $e) {
            Log::error('Error al actualizar solicitante', [
                'error' => $e->getMessage(),
                'solicitante_id' => $solicitante->id
            ]);
            throw new \Exception('Error al actualizar el solicitante: ' . $e->getMessage());
        }
    }

    /**
     * Find solicitante by RFC
     *
     * @param string $rfc
     * @return Solicitante|null
     */
    public function findByRfc(string $rfc): ?Solicitante
    {
        return Solicitante::where('rfc', $rfc)->first();
    }

    /**
     * Validate SAT data for registration
     */
    public function validateSatData(array $data): array
    {
        return \Illuminate\Support\Facades\Validator::make($data, [
            'qr_url' => ['nullable', 'string', 'url'],
            'sat_rfc' => ['required', 'string', 'max:13'],
            'sat_nombre' => ['required', 'string', 'max:255'],
            'sat_tipo_persona' => ['required', 'in:Física,Moral'],
            'sat_cp' => ['required', 'string', 'max:5'],
            'sat_nombre_vialidad' => ['nullable', 'string', 'max:255'],
        ], [
            'sat_rfc.required' => 'El RFC del SAT es obligatorio.',
            'sat_rfc.max' => 'El RFC no debe exceder 13 caracteres.',
            'sat_nombre.required' => 'El nombre/razón social del SAT es obligatorio.',
            'sat_tipo_persona.required' => 'El tipo de persona es obligatorio.',
            'sat_tipo_persona.in' => 'El tipo de persona debe ser Física o Moral.',
            'sat_cp.required' => 'El código postal es obligatorio.',
            'sat_cp.max' => 'El código postal no debe exceder 5 caracteres.',
        ])->validate();
    }
} 