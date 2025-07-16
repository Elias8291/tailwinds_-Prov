<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por rol (usando Spatie)
        if ($request->filled('rol')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->rol);
            });
        }

        // Filtro por fecha de registro
        if ($request->filled('fecha')) {
            if ($request->fecha === 'hoy') {
                $query->whereDate('created_at', today());
            } elseif ($request->fecha === 'semana') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->fecha === 'mes') {
                $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            }
        }

        // Paginación
        $perPage = $request->input('perPage', 10);
        if ($perPage === 'all') {
            $usuarios = $query->get();
        } else {
            $usuarios = $query->paginate((int) $perPage)->appends($request->all());
        }

        $totalUsuarios = User::count();
        return view('users.index', compact('totalUsuarios', 'usuarios'));
    }

    public function validateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('correo', $request->email)->exists();
        if ($exists) {
            return ['valid' => false, 'message' => 'Este email ya está registrado'];
        }
        return ['valid' => true, 'message' => 'Email disponible'];
    }

    public function validateRfc(Request $request)
    {
        $request->validate([
            'rfc' => [
                'required',
                'regex:/^[A-ZÑ&]{3,4}[0-9]{6}[A-V1-9][A-Z0-9][0-9]$/i',
            ],
        ], [
            'rfc.regex' => 'Formato de RFC inválido',
        ]);

        $exists = User::where('rfc', $request->rfc)->exists();
        if ($exists) {
            return ['valid' => false, 'message' => 'Este RFC ya está registrado'];
        }
        return ['valid' => true, 'message' => 'RFC disponible'];
    }
} 