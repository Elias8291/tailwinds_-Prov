<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index()
    {
        return view('notificaciones.index');
    }

    public function contador()
    {
        return response()->json(['contador' => 0]);
    }

    public function header()
    {
        return response()->json([
            'success' => true,
            'notificaciones' => [],
            'contador_no_leidas' => 0
        ]);
    }

    public function marcarTodasLeidas()
    {
        return response()->json(['success' => true]);
    }

    public function getUserNotifications()
    {
        $user = request()->user();
        $notificaciones = $user->notificaciones()->latest()->get();
        if ($notificaciones->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene notificaciones asociadas.'
            ]);
        }
        return response()->json([
            'success' => true,
            'notificaciones' => $notificaciones
        ]);
    }
} 