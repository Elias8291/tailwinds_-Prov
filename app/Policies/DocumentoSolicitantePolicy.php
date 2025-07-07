<?php

namespace App\Policies;

use App\Models\DocumentoSolicitante;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentoSolicitantePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the documento.
     */
    public function view(User $user, DocumentoSolicitante $documento)
    {
        // El usuario debe ser el dueño del trámite o tener permisos de revisión
        return $user->id === $documento->tramite->solicitante->user_id
            || $user->hasPermissionTo('revisar-documentos');
    }

    /**
     * Determina si el usuario puede ver el documento.
     *
     * @param User $user
     * @param DocumentoSolicitante $documentoSolicitante
     * @return bool
     */
    public function ver(User $user, DocumentoSolicitante $documentoSolicitante)
    {
        // El usuario puede ver el documento si:
        // 1. Es el solicitante del trámite
        // 2. Tiene permiso para revisar trámites
        // 3. Es un administrador
        return $documentoSolicitante->tramite->solicitante->usuario_id === $user->id ||
               $user->can('revision-tramites.ver') ||
               $user->hasRole('admin');
    }
} 