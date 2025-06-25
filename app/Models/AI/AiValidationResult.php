<?php

namespace App\Models\AI;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\DocumentoSolicitante;
use App\Models\User;

class AiValidationResult extends Model
{
    use HasFactory;

    protected $table = 'ai_validation_results';

    protected $fillable = [
        'documento_solicitante_id',
        'ai_model_id',
        'predicted_document_type',
        'confidence_score',
        'classification_results',
        'extracted_features',
        'extracted_text_summary',
        'validation_status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'processed_at',
        'processing_time_ms'
    ];

    protected $casts = [
        'classification_results' => 'array',
        'extracted_features' => 'array',
        'confidence_score' => 'decimal:4',
        'processed_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'processing_time_ms' => 'integer'
    ];

    /**
     * Relationship with documento solicitante
     */
    public function documentoSolicitante()
    {
        return $this->belongsTo(DocumentoSolicitante::class, 'documento_solicitante_id');
    }

    /**
     * Relationship with AI model
     */
    public function aiModel()
    {
        return $this->belongsTo(AiDocumentModel::class, 'ai_model_id');
    }

    /**
     * Relationship with reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Confirm the AI prediction as correct
     */
    public function confirmPrediction(User $reviewer, string $notes = null): void
    {
        $this->update([
            'validation_status' => 'human_confirmed',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $notes
        ]);
    }

    /**
     * Reject the AI prediction as incorrect
     */
    public function rejectPrediction(User $reviewer, string $reason): void
    {
        $this->update([
            'validation_status' => 'human_rejected',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $reason
        ]);
    }

    /**
     * Auto-approve high confidence predictions
     */
    public function autoApprove(): void
    {
        $this->update([
            'validation_status' => 'auto_approved',
            'reviewed_at' => now()
        ]);
    }

    /**
     * Check if prediction is high confidence
     */
    public function isHighConfidence(): bool
    {
        return $this->confidence_score >= 0.9; // 90% confidence threshold
    }

    /**
     * Check if prediction is low confidence
     */
    public function isLowConfidence(): bool
    {
        return $this->confidence_score < 0.7; // Below 70% confidence
    }

    /**
     * Get confidence percentage
     */
    public function getConfidencePercentageAttribute(): string
    {
        return number_format($this->confidence_score * 100, 2) . '%';
    }

    /**
     * Get processing time in seconds
     */
    public function getProcessingTimeSecondsAttribute(): string
    {
        return $this->processing_time_ms ? number_format($this->processing_time_ms / 1000, 3) . 's' : 'N/A';
    }

    /**
     * Scope for pending review
     */
    public function scopePendingReview($query)
    {
        return $query->where('validation_status', 'pending_review');
    }

    /**
     * Scope for high confidence predictions
     */
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence_score', '>=', 0.9);
    }

    /**
     * Scope for low confidence predictions
     */
    public function scopeLowConfidence($query)
    {
        return $query->where('confidence_score', '<', 0.7);
    }

    /**
     * Get the alternative predictions from classification results
     */
    public function getAlternativePredictions(): array
    {
        if (!$this->classification_results) {
            return [];
        }

        // Sort by confidence and exclude the main prediction
        $alternatives = collect($this->classification_results)
            ->reject(function ($result) {
                return $result['document_type'] === $this->predicted_document_type;
            })
            ->sortByDesc('confidence')
            ->take(3)
            ->values()
            ->toArray();

        return $alternatives;
    }

    /**
     * Get validation statistics
     */
    public static function getValidationStats(): array
    {
        $stats = self::selectRaw('
            COUNT(*) as total,
            COUNT(CASE WHEN validation_status = "pending_review" THEN 1 END) as pending_review,
            COUNT(CASE WHEN validation_status = "human_confirmed" THEN 1 END) as confirmed,
            COUNT(CASE WHEN validation_status = "human_rejected" THEN 1 END) as rejected,
            COUNT(CASE WHEN validation_status = "auto_approved" THEN 1 END) as auto_approved,
            AVG(confidence_score) as avg_confidence,
            AVG(processing_time_ms) as avg_processing_time
        ')->first();

        return [
            'total' => $stats->total ?? 0,
            'pending_review' => $stats->pending_review ?? 0,
            'confirmed' => $stats->confirmed ?? 0,
            'rejected' => $stats->rejected ?? 0,
            'auto_approved' => $stats->auto_approved ?? 0,
            'average_confidence' => $stats->avg_confidence ? round($stats->avg_confidence, 4) : 0,
            'average_processing_time' => $stats->avg_processing_time ? round($stats->avg_processing_time) : 0
        ];
    }
} 