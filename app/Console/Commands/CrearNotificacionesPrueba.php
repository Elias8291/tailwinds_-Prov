<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notificacion;
use App\Models\User;

class CrearNotificacionesPrueba extends Command
{
    protected $signature = 'notificaciones:crear-prueba {--usuario= : ID del usuario para crear notificaciones}';
    protected $description = 'Crea notificaciones de prueba para testing del módulo';

    public function handle()
    {
        $usuarioId = $this->option('usuario');
        
        if (!$usuarioId) {
            // Obtener todos los usuarios
            $usuarios = User::all();
            if ($usuarios->isEmpty()) {
                $this->error('No hay usuarios en el sistema');
                return 1;
            }
            
            $this->info('Usuarios disponibles:');
            foreach ($usuarios as $user) {
                $this->line("ID: {$user->id} - {$user->name} ({$user->email})");
            }
            
            $usuarioId = $this->ask('Ingresa el ID del usuario');
        }
        
        $usuario = User::find($usuarioId);
        if (!$usuario) {
            $this->error("Usuario con ID {$usuarioId} no encontrado");
            return 1;
        }
        
        $this->info("Creando notificaciones de prueba para: {$usuario->name}");
        
        // Notificaciones de ejemplo
        $notificacionesEjemplo = [
            [
                'titulo' => 'Bienvenido al sistema',
                'mensaje' => 'Te damos la bienvenida al sistema de gestión de trámites. Aquí podrás gestionar todos tus procesos de manera eficiente.',
                'tipo' => 'Informativo'
            ],
            [
                'titulo' => 'Documento pendiente de revisión',
                'mensaje' => 'Tu documento "Constitución de empresa" está pendiente de revisión por parte del equipo administrativo.',
                'tipo' => 'Advertencia'
            ],
            [
                'titulo' => 'Trámite aprobado',
                'mensaje' => 'Tu trámite de inscripción ha sido aprobado exitosamente. Puedes descargar el documento final desde tu panel.',
                'tipo' => 'Informativo'
            ],
            [
                'titulo' => 'Error en validación',
                'mensaje' => 'Se encontraron errores en la validación de tus documentos. Por favor, revisa la sección de documentos y corrige los campos marcados.',
                'tipo' => 'Error'
            ],
            [
                'titulo' => 'Cita agendada',
                'mensaje' => 'Tu cita para el día 15 de enero a las 10:00 AM ha sido confirmada. Te recordaremos un día antes.',
                'tipo' => 'Informativo'
            ],
            [
                'titulo' => 'Plazo próximo a vencer',
                'mensaje' => 'El plazo para completar tu trámite de renovación vence en 3 días. Asegúrate de completar todos los pasos requeridos.',
                'tipo' => 'Advertencia'
            ],
            [
                'titulo' => 'Sistema en mantenimiento',
                'mensaje' => 'El sistema estará en mantenimiento el próximo domingo de 2:00 AM a 6:00 AM. Durante este tiempo no podrás acceder a la plataforma.',
                'tipo' => 'Advertencia'
            ],
            [
                'titulo' => 'Actualización completada',
                'mensaje' => 'Se ha completado la actualización del sistema. Ahora tienes acceso a nuevas funcionalidades en el módulo de notificaciones.',
                'tipo' => 'Informativo'
            ]
        ];
        
        $this->info('Creando notificaciones...');
        $barra = $this->output->createProgressBar(count($notificacionesEjemplo));
        $barra->start();
        
        foreach ($notificacionesEjemplo as $index => $notif) {
            // Crear algunas como leídas y otras como no leídas
            $notificacion = Notificacion::crearParaUsuario(
                $notif['titulo'],
                $notif['mensaje'],
                $notif['tipo'],
                $usuario->id
            );
            
            // Marcar algunas como leídas (las primeras 3)
            if ($index < 3) {
                $notificacion->marcarComoLeida($usuario->id);
            }
            
            $barra->advance();
            usleep(200000); // Pequeña pausa para simular tiempo
        }
        
        $barra->finish();
        $this->newLine();
        
        $this->info('✅ Notificaciones de prueba creadas exitosamente');
        $this->line("   - Total creadas: " . count($notificacionesEjemplo));
        $this->line("   - Leídas: 3");
        $this->line("   - No leídas: " . (count($notificacionesEjemplo) - 3));
        $this->line("   - Usuario: {$usuario->name}");
        
        return 0;
    }
} 