<?php

namespace App\View\Components\Formularios;

use Illuminate\View\Component;

class SeccionDocumentos extends Component
{
    public $title;
    public $tramite;
    public $mostrar_navegacion;
    public $documentos;
    public $readonly;

    public function __construct($title = 'Documentos Requeridos', $tramite = null, $mostrar_navegacion = true, $documentos = [], $readonly = false)
    {
        $this->title = $title;
        $this->tramite = $tramite;
        $this->mostrar_navegacion = $mostrar_navegacion;
        $this->documentos = $documentos;
        $this->readonly = $readonly;
    }

    public function render()
    {
        return view('components.formularios.seccion-documentos');
    }
} 