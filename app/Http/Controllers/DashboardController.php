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
     * Muestra el dashboard del solicitante
     */
    public function solicitanteDashboard()
    {
        return view('dashboard2');
    }
} 