<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Programar la actualización automática de estados de proveedores
Schedule::command('proveedores:actualizar-estados')
    ->daily()
    ->at('00:30') // Se ejecuta todos los días a las 12:30 AM
    ->name('actualizar-estados-proveedores')
    ->description('Actualiza automáticamente el estado de proveedores vencidos')
    ->withoutOverlapping()
    ->onOneServer();
