<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ValidationController extends Controller
{
    /**
     * Verificar si un correo electrónico ya existe en la base de datos
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $email = $request->query('email');
        
        if (empty($email)) {
            return response()->json([
                'valid' => false,
                'message' => 'Correo electrónico requerido'
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'valid' => false,
                'message' => 'Formato de correo electrónico inválido'
            ]);
        }

        $exists = User::where('correo', $email)->exists();
        
        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'Este correo electrónico ya está registrado' : 'Correo electrónico disponible',
            'exists' => $exists
        ]);
    }

    /**
     * Verificar si un RFC ya existe en la base de datos
     */
    public function checkRfc(Request $request): JsonResponse
    {
        $rfc = $request->query('rfc');
        
        if (empty($rfc)) {
            return response()->json([
                'valid' => false,
                'message' => 'RFC requerido'
            ], 400);
        }

        // Validación básica de formato RFC
        $rfc = strtoupper(trim($rfc));
        if (!preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfc)) {
            return response()->json([
                'valid' => false,
                'message' => 'Formato de RFC inválido'
            ]);
        }

        $exists = User::where('rfc', $rfc)->exists();
        
        return response()->json([
            'valid' => !$exists,
            'message' => $exists ? 'Este RFC ya está registrado' : 'RFC disponible',
            'exists' => $exists
        ]);
    }

    /**
     * Verificar tanto correo como RFC en una sola consulta
     */
    public function checkBoth(Request $request): JsonResponse
    {
        $email = $request->query('email');
        $rfc = $request->query('rfc');
        
        $result = [
            'email' => ['valid' => true, 'message' => '', 'exists' => false],
            'rfc' => ['valid' => true, 'message' => '', 'exists' => false]
        ];

        // Validar email si se proporciona
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['email'] = [
                    'valid' => false,
                    'message' => 'Formato de correo electrónico inválido',
                    'exists' => false
                ];
            } else {
                $emailExists = User::where('correo', $email)->exists();
                $result['email'] = [
                    'valid' => !$emailExists,
                    'message' => $emailExists ? 'Este correo electrónico ya está registrado' : 'Correo electrónico disponible',
                    'exists' => $emailExists
                ];
            }
        }

        // Validar RFC si se proporciona
        if (!empty($rfc)) {
            $rfc = strtoupper(trim($rfc));
            if (!preg_match('/^[A-Z&Ñ]{3,4}[0-9]{6}[A-Z0-9]{3}$/', $rfc)) {
                $result['rfc'] = [
                    'valid' => false,
                    'message' => 'Formato de RFC inválido',
                    'exists' => false
                ];
            } else {
                $rfcExists = User::where('rfc', $rfc)->exists();
                $result['rfc'] = [
                    'valid' => !$rfcExists,
                    'message' => $rfcExists ? 'Este RFC ya está registrado' : 'RFC disponible',
                    'exists' => $rfcExists
                ];
            }
        }

        return response()->json($result);
    }
} 