<?php

namespace App\Events;

use App\Models\Tramite;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SolicitudCorreccionesEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tramite;

    /**
     * Create a new event instance.
     *
     * @param Tramite $tramite
     * @return void
     */
    public function __construct(Tramite $tramite)
    {
        $this->tramite = $tramite;
    }
} 