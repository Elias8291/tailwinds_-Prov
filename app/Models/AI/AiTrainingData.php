<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DocumentoSolicitante;
use App\Models\User;

class AiTrainingData extends Model
{
    use HasFactory;

    protected $table = 'ai_training_data';

    protected $fillable = [
        'documento_solicitante_id',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'expected_document_type',
        'validation_status',
        'extracted_text',
        'document_features',
        'metadata',
        'validated_by',
        'validated_at',
        'validation_notes'
    ];

    protected $casts = [
        'document_features' => 'array',
        'metadata' => 'array',
        'validated_at' => 'datetime',
        'file_size' => 'integer'
    ];

    /**
     * Relationship with documento solicitante
     */
    public function documentoSolicitante()
    {
        return $this->belongsTo(DocumentoSolicitante::class, 'documento_solicitante_id');
    }

    /**
     * Relationship with validator user
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Get validated training data
     */
    public static function getValidated()
    {
        return self::where('validation_status', 'validated')->get();
    }

    /**
     * Get training data by document type
     */
    public static function getByDocumentType(string $documentType)
    {
        return self::where('expected_document_type', $documentType)
            ->where('validation_status', 'validated')
            ->get();
    }

    /**
     * Mark as validated
     */
    public function markAsValidated(User $validator, string $notes = null): void
    {
        $this->update([
            'validation_status' => 'validated',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_notes' => $notes
        ]);
    }

    /**
     * Mark as rejected
     */
    public function markAsRejected(User $validator, string $reason): void
    {
        $this->update([
            'validation_status' => 'rejected',
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'validation_notes' => $reason
        ]);
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for pending validation
     */
    public function scopePendingValidation($query)
    {
        return $query->where('validation_status', 'pending');
    }

    /**
     * Scope for specific document types
     */
    public function scopeOfType($query, string $documentType)
    {
        return $query->where('expected_document_type', $documentType);
    }

    /**
     * Get statistics for training data
     */
    public static function getStats(): array
    {
        $stats = self::selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN validation_status = "validated" THEN 1 END) as validated,
            COUNT(CASE WHEN validation_status = "pending" THEN 1 END) as pending,
            COUNT(CASE WHEN validation_status = "rejected" THEN 1 END) as rejected,
            COUNT(CASE WHEN validation_status = "needs_review" THEN 1 END) as needs_review
        ')->first();

        return [
            'total' => $stats->total ?? 0,
            'validated' => $stats->validated ?? 0,
            'pending' => $stats->pending ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'needs_review' => $stats->needs_review ?? 0
        ];
    }

    /**
     * Get training data distribution by document type
     */
    public static function getDocumentTypeDistribution(): array
    {
        return self::where('validation_status', 'validated')
            ->selectRaw('expected_document_type, COUNT(*) as count')
            ->groupBy('expected_document_type')
            ->pluck('count', 'expected_document_type')
            ->toArray();
    }

    /**
     * Get the URL to view the file
     */
    public function getFileUrlAttribute(): string
    {
        return route('ai.training.file.serve', $this);
    }
} 