<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiDocumentModel extends Model
{
    use HasFactory;

    protected $table = 'ai_document_models';

    protected $fillable = [
        'name',
        'description',
        'status',
        'accuracy',
        'training_documents_count',
        'supported_document_types',
        'model_parameters',
        'trained_at',
        'last_used_at',
        'version',
        'is_default'
    ];

    protected $casts = [
        'supported_document_types' => 'array',
        'model_parameters' => 'array',
        'accuracy' => 'decimal:4',
        'trained_at' => 'datetime',
        'last_used_at' => 'datetime',
        'is_default' => 'boolean'
    ];

    /**
     * Relationship with validation results
     */
    public function validationResults()
    {
        return $this->hasMany(AiValidationResult::class, 'ai_model_id');
    }

    /**
     * Get the default active model
     */
    public static function getDefault(): ?self
    {
        return self::where('status', 'active')
            ->where('is_default', true)
            ->first();
    }

    /**
     * Get active models
     */
    public static function getActive()
    {
        return self::where('status', 'active')->get();
    }

    /**
     * Check if this model supports a specific document type
     */
    public function supportsDocumentType(string $documentType): bool
    {
        return in_array($documentType, $this->supported_document_types ?? []);
    }

    /**
     * Mark this model as default (and unmark others)
     */
    public function setAsDefault(): void
    {
        // First, remove default flag from all other models
        self::where('is_default', true)->update(['is_default' => false]);
        
        // Then set this model as default
        $this->update(['is_default' => true, 'status' => 'active']);
    }

    /**
     * Update the last used timestamp
     */
    public function recordUsage(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get formatted accuracy percentage
     */
    public function getAccuracyPercentageAttribute(): string
    {
        return $this->accuracy ? number_format($this->accuracy * 100, 2) . '%' : 'N/A';
    }

    /**
     * Scope for ready models
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'active')->whereNotNull('trained_at');
    }

    /**
     * Get the model's performance stats
     */
    public function getPerformanceStats(): array
    {
        $results = $this->validationResults()
            ->selectRaw('
                COUNT(*) as total_predictions,
                AVG(confidence_score) as avg_confidence,
                COUNT(CASE WHEN validation_status = "human_confirmed" THEN 1 END) as confirmed_predictions,
                COUNT(CASE WHEN validation_status = "human_rejected" THEN 1 END) as rejected_predictions
            ')
            ->first();

        return [
            'total_predictions' => $results->total_predictions ?? 0,
            'average_confidence' => $results->avg_confidence ? round($results->avg_confidence, 4) : 0,
            'confirmed_predictions' => $results->confirmed_predictions ?? 0,
            'rejected_predictions' => $results->rejected_predictions ?? 0,
            'accuracy_rate' => $results->total_predictions > 0 
                ? round(($results->confirmed_predictions / $results->total_predictions) * 100, 2) 
                : 0
        ];
    }
} 