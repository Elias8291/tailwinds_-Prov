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
    public $en_revision;
    
    // ✨ NUEVAS PROPIEDADES PARA ARCHIVOS PESADOS
    public $max_file_size;
    public $allowed_extensions;
    public $upload_timeout;
    public $chunk_size;
    public $enable_progress;

    public function __construct(
        $title = 'Documentos Requeridos', 
        $tramite = null, 
        $mostrar_navegacion = true, 
        $documentos = [], 
        $readonly = false,
        $en_revision = false,
        $max_file_size = 104857600, // 100MB por defecto
        $allowed_extensions = ['pdf'],
        $upload_timeout = 300, // 5 minutos
        $chunk_size = 1048576, // 1MB chunks
        $enable_progress = true
    ) {
        $this->title = $title;
        $this->tramite = $tramite;
        $this->mostrar_navegacion = $mostrar_navegacion;
        $this->documentos = $documentos;
        $this->readonly = $readonly;
        $this->en_revision = $en_revision;
        
        // ✨ Configuración para archivos pesados
        $this->max_file_size = $max_file_size;
        $this->allowed_extensions = $allowed_extensions;
        $this->upload_timeout = $upload_timeout;
        $this->chunk_size = $chunk_size;
        $this->enable_progress = $enable_progress;
    }
    
    /**
     * Obtiene el tamaño máximo de archivo formateado
     */
    public function getMaxFileSizeFormatted()
    {
        return $this->formatBytes($this->max_file_size);
    }
    
    /**
     * Obtiene las extensiones permitidas como string
     */
    public function getAllowedExtensionsString()
    {
        return implode(', ', array_map(function($ext) {
            return '.' . strtoupper($ext);
        }, $this->allowed_extensions));
    }
    
    /**
     * Obtiene las extensiones para el atributo accept del input
     */
    public function getAcceptAttribute()
    {
        return implode(',', array_map(function($ext) {
            return '.' . $ext;
        }, $this->allowed_extensions));
    }
    
    /**
     * Verifica si la subida de archivos pesados está habilitada
     */
    public function isHeavyFileUploadEnabled()
    {
        return $this->max_file_size > 50 * 1024 * 1024; // Más de 50MB se considera pesado
    }
    
    /**
     * Obtiene la configuración de JavaScript para el frontend
     */
    public function getJavaScriptConfig()
    {
        return [
            'maxFileSize' => $this->max_file_size,
            'allowedExtensions' => $this->allowed_extensions,
            'uploadTimeout' => $this->upload_timeout * 1000, // Convertir a millisegundos
            'chunkSize' => $this->chunk_size,
            'enableProgress' => $this->enable_progress,
            'maxFileSizeFormatted' => $this->getMaxFileSizeFormatted(),
            'allowedExtensionsString' => $this->getAllowedExtensionsString()
        ];
    }
    
    /**
     * Formatea bytes en formato legible
     */
    private function formatBytes($size, $precision = 2)
    {
        if ($size == 0) {
            return '0 B';
        }
        
        $base = log($size, 1024);
        $suffixes = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
    }
    
    /**
     * Determina si se debe mostrar el indicador de progreso
     */
    public function shouldShowProgress()
    {
        return $this->enable_progress && !$this->readonly;
    }
    
    /**
     * Obtiene el mensaje descriptivo para el tipo de archivos
     */
    public function getFileTypeDescription()
    {
        $description = $this->getAllowedExtensionsString();
        $description .= ', máximo ' . $this->getMaxFileSizeFormatted();
        
        if ($this->isHeavyFileUploadEnabled()) {
            $description .= ' (Subida optimizada para archivos pesados)';
        }
        
        return $description;
    }

    public function render()
    {
        return view('components.formularios.seccion-documentos');
    }
} 