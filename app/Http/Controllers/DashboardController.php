<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Tramite;
use App\Models\Proveedor;
use App\Models\Cita;

class DashboardController extends Controller
{
    /**
     * Dashboard principal que redirige según el rol del usuario
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar permisos y redirigir al dashboard apropiado
        if ($user->can('dashboard.admin') || $user->can('dashboard.revisor')) {
            return $this->adminDashboard();
        } elseif ($user->can('dashboard.solicitante')) {
            return $this->solicitanteDashboard();
        }
        
        // Si no tiene permisos específicos, mostrar dashboard básico
        return $this->dashboardBasico();
    }

    /**
     * Muestra el dashboard administrativo
     */
    public function adminDashboard()
    {
        // Obtener estadísticas para usuarios admin
        $totalUsuarios = User::count();
        $tramitesPendientes = Tramite::whereIn('estado', ['Pendiente', 'En Revision', 'Por Cotejar'])->count();
        $totalProveedores = Proveedor::count();
        $citasHoy = Cita::whereDate('fecha_hora', today())->count();
        $totalCitas = Cita::count();
        
        return view('dashboard', compact(
            'totalUsuarios',
            'tramitesPendientes', 
            'totalProveedores',
            'citasHoy',
            'totalCitas'
        ));
    }

    /**
     * Muestra el dashboard del solicitante con datos personalizados
     */
    public function solicitanteDashboard()
    {
        $user = Auth::user();
        
        // Cargar solicitante con sus relaciones y conteos
        $solicitante = $user->solicitante;
        
        if ($solicitante) {
            $solicitante->loadCount([
                'tramites',
                'citas as citas_pendientes_count' => function ($query) {
                    $query->where('estado', 'pendiente')
                          ->where('fecha_cita', '>=', now());
                }
            ]);
        }

        // Contar documentos pendientes si existen tramites
        $documentos_pendientes_count = 0;
        if ($solicitante && $solicitante->tramites()->exists()) {
            $documentos_pendientes_count = \App\Models\DocumentoSolicitante::whereHas('tramite', function($query) use ($solicitante) {
                $query->where('solicitante_id', $solicitante->id);
            })->where('estado', 'pendiente')->count();
        }

        return view('dashboard2', compact('user', 'solicitante', 'documentos_pendientes_count'));
    }

    /**
     * Dashboard básico para usuarios sin permisos específicos
     */
    public function dashboardBasico()
    {
        $user = Auth::user();
        
        return view('dashboard', [
            'totalUsuarios' => 0,
            'tramitesPendientes' => 0,
            'totalProveedores' => 0,
            'citasHoy' => 0,
            'totalCitas' => 0,
            'mensaje' => 'Bienvenido al sistema. Contacta al administrador para obtener los permisos necesarios.'
        ]);
    }
} 