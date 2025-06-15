<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Asentamiento;
use App\Models\Tramite;
use App\Models\DetalleTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DireccionController extends Controller
{
    /**
     * Create a new direccion record
     *
     * @param array $data
     * @return Direccion
     */
    public function create(array $data): Direccion
    {
        try {
            // Buscar asentamiento por código postal si está disponible
            $asentamientoId = null;
            if (!empty($data['codigo_postal'])) {
                $asentamiento = Asentamiento::where('codigo_postal', $data['codigo_postal'])->first();
                $asentamientoId = $asentamiento ? $asentamiento->id : null;
            }

            $direccion = Direccion::create([
                'codigo_postal' => intval($data['codigo_postal'] ?? '00000'),
                'asentamiento_id' => $asentamientoId,
                'calle' => $data['calle'] ?? null,
                'numero_exterior' => $data['numero_exterior'] ?? null,
                'numero_interior' => $data['numero_interior'] ?? null,
                'entre_calle_1' => $data['entre_calle_1'] ?? null,
                'entre_calle_2' => $data['entre_calle_2'] ?? null,
            ]);

            Log::info('Dirección creada exitosamente', [
                'direccion_id' => $direccion->id,
                'codigo_postal' => $direccion->codigo_postal
            ]);

            return $direccion;

        } catch (\Exception $e) {
            Log::error('Error al crear dirección', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw new \Exception('Error al crear la dirección: ' . $e->getMessage());
        }
    }

    /**
     * Update direccion record
     *
     * @param Direccion $direccion
     * @param array $data
     * @return Direccion
     */
    public function update(Direccion $direccion, array $data): Direccion
    {
        try {
            $direccion->update($data);
            
            Log::info('Dirección actualizada exitosamente', [
                'direccion_id' => $direccion->id
            ]);

            return $direccion;

        } catch (\Exception $e) {
            Log::error('Error al actualizar dirección', [
                'error' => $e->getMessage(),
                'direccion_id' => $direccion->id
            ]);
            throw new \Exception('Error al actualizar la dirección: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los datos formateados de la dirección para un trámite
     *
     * @param Tramite $tramite
     * @return array
     */
    public function getAddressData(Tramite $tramite)
    {
        $addressData = [
            'codigo_postal' => 'No disponible',
            'estado' => 'No disponible',
            'municipio' => 'No disponible',
            'colonia' => 'No disponible',
            'calle' => 'No disponible',
            'numero_exterior' => 'No disponible',
            'numero_interior' => 'No disponible',
            'entre_calle_1' => 'No disponible',
            'entre_calle_2' => 'No disponible',
        ];

        $detalle = DetalleTramite::where('tramite_id', $tramite->id)->first();

        if ($detalle && $detalle->direccion) {
            $this->fillAddressData($detalle->direccion, $addressData);
        }

        return $addressData;
    }

    /**
     * Guarda los datos de dirección para un trámite
     *
     * @param Request $request
     * @param Tramite $tramite
     * @return bool
     */
    public function guardar(Request $request, Tramite $tramite)
    {
        $this->validateRequest($request);

        return DB::transaction(function () use ($request, $tramite) {
            $detalle = DetalleTramite::firstOrNew(['tramite_id' => $tramite->id]);
            $direccion = $this->guardarDireccion($request, $detalle->direccion_id);
            $detalle->direccion_id = $direccion->id;
            $detalle->save();

            return true;
        });
    }

    /**
     * Guarda los datos de domicilio desde AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarFormulario(Request $request)
    {
        try {
            Log::info('=== INICIO guardarFormulario domicilio ===', [
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            // Validar los datos del formulario
            $this->validateRequest($request);

            // Buscar el trámite
            $tramite = Tramite::with('detalleTramite')->find($request->input('tramite_id'));
            if (!$tramite) {
                throw new \Exception('Trámite no encontrado');
            }

            // Guardar los datos usando el método de la clase
            $this->guardar($request, $tramite);

            Log::info('✅ Domicilio guardado exitosamente', [
                'tramite_id' => $tramite->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Datos de domicilio guardados correctamente.',
                'step' => 3
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('❌ Error de validación en domicilio:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error al guardar datos de domicilio:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene datos de dirección por código postal
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerDatosPorCodigoPostal(Request $request)
    {
        $this->validateRequest($request, 'obtenerDatosPorCodigoPostal');

        $response = Http::post(route('inscripcion.obtener-datos-direccion'), [
            'codigo_postal' => $request->input('codigo_postal'),
        ]);

        return response()->json($response->json());
    }

    /**
     * Valida los datos de la petición
     *
     * @param Request $request
     * @param string $method
     * @return void
     */
    private function validateRequest(Request $request, string $method = 'guardar')
    {
        $rules = $method === 'guardar'
            ? [
                'tramite_id' => 'required|integer|exists:tramite,id',
                'codigo_postal' => 'required|string|regex:/^\d{4,5}$/',
                'colonia' => 'required|integer|exists:asentamiento,id', 
                'calle' => 'required|string|max:100',
                'numero_exterior' => 'required|string|max:50',
                'numero_interior' => 'nullable|string|max:50',
                'entre_calle_1' => 'required|string|max:255',
                'entre_calle_2' => 'required|string|max:255',
            ]
            : [
                'codigo_postal' => 'required|string|size:5',
            ];

        $request->validate($rules);
    }

    /**
     * Llena los datos de dirección
     *
     * @param Direccion $direccion
     * @param array $addressData
     * @return void
     */
    private function fillAddressData(Direccion $direccion, array &$addressData)
    {
        $addressData['codigo_postal'] = $direccion->codigo_postal ?? 'No disponible';
        $addressData['calle'] = $direccion->calle ?? 'No disponible';
        $addressData['numero_exterior'] = $direccion->numero_exterior ?? 'No disponible';
        $addressData['numero_interior'] = $direccion->numero_interior ?? 'No disponible';
        $addressData['entre_calle_1'] = $direccion->entre_calle_1 ?? 'No disponible';
        $addressData['entre_calle_2'] = $direccion->entre_calle_2 ?? 'No disponible';

        if ($direccion->asentamiento) {
            $addressData['colonia'] = $direccion->asentamiento->nombre ?? 'No disponible';
            if ($direccion->asentamiento->localidad && $direccion->asentamiento->localidad->municipio) {
                $addressData['municipio'] = $direccion->asentamiento->localidad->municipio->nombre ?? 'No disponible';
                $addressData['estado'] = $direccion->asentamiento->localidad->municipio->estado->nombre ?? 'No disponible';
            }
        }
    }

    /**
     * Guarda o actualiza la dirección
     *
     * @param Request $request
     * @param int|null $direccionId
     * @return Direccion
     */
    private function guardarDireccion(Request $request, ?int $direccionId): Direccion
    {
        $direccion = $direccionId ? Direccion::find($direccionId) : new Direccion();
        $codigoPostal = str_pad($request->input('codigo_postal'), 5, '0', STR_PAD_LEFT);
        $asentamiento = $this->getAsentamiento($codigoPostal, $request->input('colonia'));

        $direccion->codigo_postal = $codigoPostal;
        $direccion->asentamiento_id = $asentamiento?->id;
        $direccion->calle = $request->input('calle');
        $direccion->numero_exterior = $request->input('numero_exterior');
        $direccion->numero_interior = $request->input('numero_interior');
        $direccion->entre_calle_1 = $request->input('entre_calle_1');
        $direccion->entre_calle_2 = $request->input('entre_calle_2');
        $direccion->save();

        return $direccion;
    }

    /**
     * Obtiene el asentamiento
     *
     * @param string $codigoPostal
     * @param string $coloniaId
     * @return Asentamiento|null
     */
    private function getAsentamiento(string $codigoPostal, string $coloniaId): ?Asentamiento
    {
        return Asentamiento::where('codigo_postal', $codigoPostal)
            ->where('id', $coloniaId)
            ->first();
    }
} 