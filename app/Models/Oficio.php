<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Oficio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tramite_id',
        'numero_oficio',
        'tipo_oficio',
        'ruta_archivo',
        'hash_archivo',
        'estado',
        'observaciones',
        'generado_por'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Obtener el trámite asociado al oficio
     */
    public function tramite()
    {
        return $this->belongsTo(Tramite::class);
    }

    /**
     * Obtener el usuario que generó el oficio
     */
    public function generador()
    {
        return $this->belongsTo(User::class, 'generado_por');
    }

    /**
     * Verificar el hash del archivo
     */
    public function verificarHash(): bool
    {
        if (!$this->hash_archivo || !$this->ruta_archivo) {
            return false;
        }

        if (!Storage::exists($this->ruta_archivo)) {
            return false;
        }

        $contenidoActual = Storage::get($this->ruta_archivo);
        $hashActual = hash('sha256', $contenidoActual);

        return $hashActual === $this->hash_archivo;
    }

    /**
     * Generar un nuevo número de oficio
     */
    public static function generarNumeroOficio(): string
    {
        $año = date('Y');
        $ultimoOficio = self::whereYear('created_at', $año)
            ->orderBy('id', 'desc')
            ->first();

        $consecutivo = $ultimoOficio ? intval(substr($ultimoOficio->numero_oficio, -4)) + 1 : 1;

        return sprintf('OFICIO-PV-%s-%04d', $año, $consecutivo);
    }

    /**
     * Generar el hash para un archivo
     */
    public static function generarHashArchivo($contenido): string
    {
        return hash('sha256', $contenido);
    }

    /**
     * Obtener la ruta completa del archivo
     */
    public function getRutaCompleta(): string
    {
        return storage_path('app/' . $this->ruta_archivo);
    }

    /**
     * Obtener el tipo de oficio en formato legible
     */
    public function getTipoOficioFormateado(): string
    {
        $tipos = [
            'inscripcion' => 'Inscripción',
            'renovacion' => 'Renovación',
            'actualizacion' => 'Actualización',
            'cancelacion' => 'Cancelación'
        ];

        return $tipos[$this->tipo_oficio] ?? ucfirst($this->tipo_oficio);
    }

    /**
     * Obtener el estado en formato legible
     */
    public function getEstadoFormateado(): string
    {
        $estados = [
            'generado' => 'Generado',
            'firmado' => 'Firmado',
            'entregado' => 'Entregado',
            'cancelado' => 'Cancelado'
        ];

        return $estados[$this->estado] ?? ucfirst($this->estado);
    }
} 