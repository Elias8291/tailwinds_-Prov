<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::all();
        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        return view('documentos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Física,Moral,Ambas',
            'descripcion' => 'nullable|string|max:1000',
            'es_visible' => 'boolean',
        ]);

        Documento::create($request->all());

        return redirect()->route('documentos.index')
            ->with('success', 'Documento creado exitosamente.');
    }

    public function edit(Documento $documento)
    {
        return view('documentos.edit', compact('documento'));
    }

    public function update(Request $request, Documento $documento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_persona' => 'required|in:Física,Moral,Ambas',
            'descripcion' => 'nullable|string|max:1000',
            'es_visible' => 'boolean',
        ]);

        $documento->update($request->all());

        return redirect()->route('documentos.index')
            ->with('success', 'Documento actualizado exitosamente.');
    }

    public function destroy(Documento $documento)
    {
        $documento->delete();

        return redirect()->route('documentos.index')
            ->with('success', 'Documento eliminado exitosamente.');
    }

    /**
     * Find or create constancia fiscal document
     *
     * @return Documento
     */
    public function findOrCreateConstanciaFiscal(): Documento
    {
        // Buscar el documento de constancia de situación fiscal
        $documento = Documento::where('nombre', 'LIKE', '%Constancia%Situación%Fiscal%')
            ->orWhere('nombre', 'LIKE', '%Constancia de Situación Fiscal%')
            ->first();

        if (!$documento) {
            // Crear el documento si no existe
            $documento = Documento::create([
                'nombre' => 'Constancia de Situación Fiscal',
                'tipo_persona' => 'Ambas',
                'descripcion' => 'Constancia de Situación Fiscal del SAT con código QR',
                'es_visible' => true,
            ]);
            
            \Illuminate\Support\Facades\Log::info('Documento creado', ['documento_id' => $documento->id]);
        }

        return $documento;
    }

    /**
     * Process and store SAT document for registration
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \App\Models\Tramite $tramite
     * @return void
     */
    public function processConstanciaDocument($file, \App\Models\Tramite $tramite): void
    {
        try {
            $documento = $this->findOrCreateConstanciaFiscal();

            // Generar nombre único para el archivo
            $extension = $file->getClientOriginalExtension();
            $nombreArchivo = uniqid('constancia_' . $documento->id . '_') . '.' . $extension;
            
            // Almacenar archivo
            $ruta = $file->storeAs('documentos_solicitante/' . $tramite->id, $nombreArchivo, 'public');
            $rutaEncriptada = \Illuminate\Support\Facades\Crypt::encryptString($ruta);

            // Crear registro en documento_solicitante
            \App\Models\DocumentoSolicitante::create([
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'fecha_entrega' => now(),
                'estado' => 'Aprobado',
                'version_documento' => 1,
                'ruta_archivo' => $rutaEncriptada,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\Log::info('Documento procesado y almacenado', [
                'tramite_id' => $tramite->id,
                'documento_id' => $documento->id,
                'ruta' => $ruta
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al procesar documento', [
                'error' => $e->getMessage(),
                'tramite_id' => $tramite->id
            ]);
            throw new \Exception('Error al procesar el documento: ' . $e->getMessage());
        }
    }

    /**
     * Validate document upload for registration
     */
    public function validateRegistrationDocument(array $data): array
    {
        return \Illuminate\Support\Facades\Validator::make($data, [
            'document' => ['required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:5120'],
        ], [
            'document.required' => 'El documento es obligatorio.',
            'document.mimes' => 'El documento debe ser PDF, PNG, JPG o JPEG.',
            'document.max' => 'El documento no debe exceder 5MB.',
        ])->validate();
    }
} 