<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TramiteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Obtener trámites a través del solicitante
        $tramites = $user->solicitante ? $user->solicitante->tramites()->latest()->get() : collect();
        return view('tramites.index', compact('tramites'));
    }
} 