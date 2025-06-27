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

// Programar la eliminación automática de trámites vencidos (48 horas)
Schedule::command('tramites:eliminar-vencidos --force')
    ->hourly() // Se ejecuta cada hora para verificar trámites vencidos
    ->name('eliminar-tramites-vencidos')
    ->description('Elimina automáticamente trámites que han pasado 48 horas sin completarse')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();
