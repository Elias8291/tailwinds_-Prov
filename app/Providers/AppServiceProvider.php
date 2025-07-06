<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\Formularios\SeccionDocumentos;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar componentes Blade
        Blade::componentNamespace('App\\View\\Components\\Formularios', 'formularios');
        
        // Registrar componentes de formularios
        Blade::component('formularios.seccion-documentos', SeccionDocumentos::class);
    }
}
