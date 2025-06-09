<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard administrativo
     */
    public function adminDashboard()
    {
        return view('dashboard');
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
} 