<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ai_document_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['training', 'active', 'inactive', 'deprecated'])->default('training');
            $table->decimal('accuracy', 5, 4)->nullable(); // e.g., 0.9542 for 95.42%
            $table->integer('training_documents_count')->default(0);
            $table->json('supported_document_types')->nullable(); // Array of document types this model can classify
            $table->json('model_parameters')->nullable(); // Store model configuration
            $table->timestamp('trained_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->string('version', 20)->default('1.0.0');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['status', 'is_default']);
            $table->index('trained_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('ai_document_models');
    }
}; 