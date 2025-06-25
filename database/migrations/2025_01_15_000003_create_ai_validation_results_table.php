<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_validation_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_solicitante_id');
            $table->unsignedBigInteger('ai_model_id');
            $table->string('predicted_document_type');
            $table->decimal('confidence_score', 5, 4); // 0.0000 to 1.0000
            $table->json('classification_results')->nullable(); // Detailed results for all possible classes
            $table->json('extracted_features')->nullable(); // Features extracted from the document
            $table->text('extracted_text_summary')->nullable(); // Summary of extracted text
            $table->enum('validation_status', ['pending_review', 'human_confirmed', 'human_rejected', 'auto_approved'])->default('pending_review');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('processed_at');
            $table->integer('processing_time_ms')->nullable(); // Time taken to process in milliseconds
            $table->timestamps();
            
            $table->foreign('documento_solicitante_id')->references('id')->on('documento_solicitante')->onDelete('cascade');
            $table->foreign('ai_model_id')->references('id')->on('ai_document_models')->onDelete('cascade');
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['predicted_document_type', 'confidence_score']);
            $table->index(['validation_status', 'processed_at']);
            $table->index('confidence_score');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_validation_results');
    }
}; 