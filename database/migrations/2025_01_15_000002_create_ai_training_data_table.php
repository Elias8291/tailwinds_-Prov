<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_training_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('documento_solicitante_id')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type'); // pdf, jpg, png, etc.
            $table->bigInteger('file_size'); // in bytes
            $table->string('expected_document_type'); // What type this document should be classified as
            $table->enum('validation_status', ['pending', 'validated', 'rejected', 'needs_review'])->default('pending');
            $table->text('extracted_text')->nullable();
            $table->json('document_features')->nullable(); // Extracted features for ML
            $table->json('metadata')->nullable(); // Additional document metadata
            $table->unsignedBigInteger('validated_by')->nullable(); // User who validated this training data
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('documento_solicitante_id')->references('id')->on('documento_solicitante')->onDelete('set null');
            $table->foreign('validated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['expected_document_type', 'validation_status']);
            $table->index('file_type');
            $table->index('validated_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_training_data');
    }
}; 