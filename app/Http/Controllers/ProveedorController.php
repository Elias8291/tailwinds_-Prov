<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use App\Models\Solicitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedor::with(['solicitante', 'detallesTramite']);

        // Búsqueda general (barra de búsqueda principal)
        if ($request->filled('search')) {
            $search = trim($request->get('search'));
            $query->where(function($q) use ($search) {
                // Búsqueda por PV
                $q->where('pv', 'LIKE', '%' . $search . '%');
                // Búsqueda por RFC
                $q->orWhereHas('solicitante', function($sq) use ($search) {
                    $sq->where('rfc', 'LIKE', '%' . $search . '%');
                });
                // Búsqueda por razón social
                $q->orWhereHas('detallesTramite', function($sq) use ($search) {
                    $sq->where('razon_social', 'LIKE', '%' . $search . '%');
                });
            });
        }

        // Búsqueda específica por RFC (barra de búsqueda superior)
        if ($request->filled('rfc')) {
            $rfc = trim($request->get('rfc'));
            $query->where(function($q) use ($rfc) {
                // Búsqueda por PV
                $q->where('pv', 'LIKE', '%' . $rfc . '%');
                // Búsqueda por RFC
                $q->orWhereHas('solicitante', function($sq) use ($rfc) {
                    $sq->where('rfc', 'LIKE', '%' . $rfc . '%');
                });
                // Búsqueda por razón social
                $q->orWhereHas('detallesTramite', function($sq) use ($rfc) {
                    $sq->where('razon_social', 'LIKE', '%' . $rfc . '%');
                });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        // Para debug - Imprimir la consulta SQL generada
        \Log::info('SQL Query:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings()
        ]);

        $perPage = $request->get('perPage', 10);
        $proveedores = $query->paginate($perPage)->withQueryString();

        // Si hay una búsqueda por RFC, determinar los trámites disponibles
        $tramitesDisponibles = [];
        if ($request->filled('rfc')) {
            $proveedor = $proveedores->first();
            if ($proveedor) {
                $tramitesDisponibles = $this->determinarTramitesDisponibles($proveedor);
            }
        }

        return view('proveedores.index', compact('proveedores', 'tramitesDisponibles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pv' => 'required|unique:proveedor,pv',
            'rfc' => 'required',
            'tipo_persona' => 'required|in:Fisica,Moral',
            'nombre' => 'required_if:tipo_persona,Fisica',
            'razon_social' => 'required_if:tipo_persona,Moral',
            'estado' => 'required|in:Activo,Inactivo,Pendiente Renovacion',
            'fecha_registro' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_registro',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            // Crear o actualizar solicitante
            $solicitante = Solicitante::updateOrCreate(
                ['rfc' => $request->rfc],
                [
                    'tipo_persona' => $request->tipo_persona,
                    'nombre' => $request->nombre,
                    'razon_social' => $request->razon_social
                ]
            );

            // Crear proveedor
            $proveedor = new Proveedor([
                'pv' => $request->pv,
                'solicitante_id' => $solicitante->id,
                'estado' => $request->estado,
                'fecha_registro' => Carbon::parse($request->fecha_registro),
                'fecha_vencimiento' => Carbon::parse($request->fecha_vencimiento),
                'observaciones' => $request->observaciones
            ]);

            $proveedor->save();

            DB::commit();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al registrar el proveedor: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $pv)
    {
        $proveedor = Proveedor::with('solicitante')->where('pv', $pv)->firstOrFail();
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $pv)
    {
        $request->validate([
            'rfc' => 'required',
            'tipo_persona' => 'required|in:Fisica,Moral',
            'nombre' => 'required_if:tipo_persona,Fisica',
            'razon_social' => 'required_if:tipo_persona,Moral',
            'estado' => 'required|in:Activo,Inactivo,Pendiente Renovacion',
            'fecha_registro' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_registro',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $proveedor = Proveedor::where('pv', $pv)->firstOrFail();
            $solicitante = $proveedor->solicitante;

            // Actualizar solicitante
            $solicitante->update([
                'rfc' => $request->rfc,
                'tipo_persona' => $request->tipo_persona,
                'nombre' => $request->nombre,
                'razon_social' => $request->razon_social
            ]);

            // Actualizar proveedor
            $proveedor->update([
                'estado' => $request->estado,
                'fecha_registro' => Carbon::parse($request->fecha_registro),
                'fecha_vencimiento' => Carbon::parse($request->fecha_vencimiento),
                'observaciones' => $request->observaciones
            ]);

            DB::commit();
            return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $pv)
    {
        try {
            $proveedor = Proveedor::where('pv', $pv)->firstOrFail();
            $proveedor->delete();
            
            return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Determina los trámites disponibles para un proveedor basado en su estado y fechas.
     */
    private function determinarTramitesDisponibles(Proveedor $proveedor)
    {
        $tramites = [];

        // Si no existe el proveedor, está disponible la inscripción
        if (!$proveedor) {
            $tramites[] = [
                'tipo' => 'inscripcion',
                'nombre' => 'Inscripción de Proveedor',
                'descripcion' => 'Registro inicial como proveedor'
            ];
            return $tramites;
        }

        // Verificar si está próximo a vencer o vencido
        $diasParaVencer = $proveedor->dias_para_vencer;
        
        if ($diasParaVencer !== null) {
            if ($diasParaVencer <= 30 && $diasParaVencer > 0) {
                $tramites[] = [
                    'tipo' => 'renovacion',
                    'nombre' => 'Renovación de Registro',
                    'descripcion' => 'Su registro vencerá en ' . $diasParaVencer . ' días'
                ];
            } elseif ($diasParaVencer <= 0) {
                $tramites[] = [
                    'tipo' => 'renovacion',
                    'nombre' => 'Renovación de Registro (Vencido)',
                    'descripcion' => 'Su registro está vencido'
                ];
            }
        }

        // Si está activo, puede modificar sus datos
        if ($proveedor->estado === 'Activo') {
            $tramites[] = [
                'tipo' => 'modificacion',
                'nombre' => 'Modificación de Datos',
                'descripcion' => 'Actualizar información del registro'
            ];
        }

        return $tramites;
    }
} 