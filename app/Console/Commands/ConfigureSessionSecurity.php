<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureSessionSecurity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:configure-security {--lifetime=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configura la seguridad de sesiones para prevenir navegación con botones del navegador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $lifetime = $this->option('lifetime');
        
        $this->info('Configurando seguridad de sesiones...');
        
        // Verificar si existe el archivo .env
        if (!File::exists(base_path('.env'))) {
            if (File::exists(base_path('.env.example'))) {
                File::copy(base_path('.env.example'), base_path('.env'));
                $this->info('Archivo .env creado desde .env.example');
            } else {
                $this->error('No se pudo encontrar .env ni .env.example');
                return 1;
            }
        }
        
        $envPath = base_path('.env');
        $envContent = File::get($envPath);
        
        // Configuraciones de sesión
        $sessionConfigs = [
            'SESSION_DRIVER' => 'database',
            'SESSION_LIFETIME' => $lifetime,
            'SESSION_EXPIRE_ON_CLOSE' => 'false',
            'SESSION_ENCRYPT' => 'false',
        ];
        
        foreach ($sessionConfigs as $key => $value) {
            $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';
            $replacement = $key . '=' . $value;
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
                $this->info("Actualizado: {$key}={$value}");
            } else {
                $envContent .= "\n{$replacement}";
                $this->info("Agregado: {$key}={$value}");
            }
        }
        
        File::put($envPath, $envContent);
        
        $this->info('✓ Configuración de sesiones actualizada');
        $this->info("✓ Tiempo de vida de sesión: {$lifetime} minutos");
        $this->warn('⚠ Ejecuta "php artisan config:cache" para aplicar los cambios');
        
        return 0;
    }
}
