<?php

namespace App\View\Components\Formularios;

use Illuminate\View\Component;

class SeccionTerminos extends Component
{
    public $tipoTramite;
    public $rfc;
    public $tipoPersona;

    public function __construct($tipoTramite = 'inscripcion', $rfc = '', $tipoPersona = 'Física')
    {
        $this->tipoTramite = $tipoTramite;
        $this->rfc = $rfc;
        $this->tipoPersona = $tipoPersona;
    }

    public function render()
    {
        return view('components.formularios.seccion-terminos');
    }
} 