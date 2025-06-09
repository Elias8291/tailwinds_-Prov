<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-permissions {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user permissions for debugging';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('correo', $email)->first();

        if (!$user) {
            $this->error("Usuario no encontrado con email: {$email}");
            return;
        }

        $this->info("Usuario: {$user->nombre}");
        $this->info("Email: {$user->correo}");
        $this->info("RFC: {$user->rfc}");
        $this->info("Estado: {$user->estado}");
        
        $roles = $user->roles->pluck('name')->toArray();
        $this->info("Roles: " . implode(', ', $roles));
        
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        $this->info("Permisos: " . implode(', ', $permissions));

        $this->info("¿Puede dashboard.admin? " . ($user->can('dashboard.admin') ? 'SÍ' : 'NO'));
        $this->info("¿Puede dashboard.solicitante? " . ($user->can('dashboard.solicitante') ? 'SÍ' : 'NO'));
    }
}
