<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->timestamp('fecha_verificacion_correo')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('rfc');
            $table->timestamp('ultimo_acceso')->nullable();
            $table->enum('estado', ['pendiente', 'activo', 'inactivo', 'suspendido'])->default('pendiente');
            $table->string('verification_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('correo')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};