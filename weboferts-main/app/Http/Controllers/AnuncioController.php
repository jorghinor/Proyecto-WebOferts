<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anuncio;
use App\Models\AnuncioPaquete;
use App\Models\Paquete;
use App\Models\Foto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Image;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnuncioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'titulo' => 'required|string|max:190',
                'descripcion' => 'required|string',
                'negocio_id' => 'required|integer|exists:negocios,id',
                'producto_id' => 'required|integer|exists:productos,id',
                'paquete_id' => 'required|integer|exists:paquetes,id',
                'imagen' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5000',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $anuncio = new Anuncio();
            $anuncio->titulo = $request->titulo;
            $anuncio->descripcion = $request->descripcion;
            $anuncio->negocio_id = $request->negocio_id;
            $anuncio->producto_id = $request->producto_id;
            $anuncio->estadoanuncio = 'activo';
            // Generar un código único para el anuncio
            $anuncio->codigo = strtoupper(Str::random(10));
            $anuncio->save();

            // Handle image upload
            if ($request->hasFile('imagen')) {
                $file = $request->file('imagen');
                $imageUpload = Image::make($file);
                $originalPath = storage_path('app/public/images');
                $timeName = time().'.'.$file->getClientOriginalExtension();

                // Ensure directory exists
                if (!file_exists($originalPath)) {
                    mkdir($originalPath, 0777, true);
                }

                $imageUpload->save($originalPath.'/'.$timeName);
                $url = Storage::url('images/'.$timeName);

                $thumbnailPath = storage_path('app/public/images/thumbnail');
                if (!file_exists($thumbnailPath)) {
                    mkdir($thumbnailPath, 0777, true);
                }
                $imageUpload->resize(270,280);
                $imageUpload->save($thumbnailPath.'/'.$timeName);

                $foto = new Foto;
                $foto->url = $url;
                $foto->anuncio_id = $anuncio->id;
                $foto->save();
            }

            // Handle package association
            $paquete = Paquete::find($request->paquete_id);
            if ($paquete) {
                $anuncio->paquetes()->attach($paquete->id, [
                    'tipo' => $paquete->tipopaquete,
                    'costo' => $paquete->costo,
                    'orden' => $paquete->orden,
                    'fechafin' => now()->addDays($paquete->tiempo),
                    'tiempo' => $paquete->tiempo,
                    'estadocompra' => 'activo',
                    'codigopago' => 'ADMIN_CREATED',
                    'label' => $paquete->label,
                    'color' => $paquete->color,
                ]);
            }

            $anuncio->load('fotos', 'producto', 'negocio', 'anuncioPaquete.paquete');

            return response()->json(['success' => true, 'data' => $anuncio], 201);

        } catch (\Exception $e) {
            Log::error('Error creating anuncio: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:190',
            'descripcion' => 'required|string',
            'producto_id' => 'sometimes|integer|exists:productos,id',
            'paquetes' => 'sometimes|array', // Validate paquetes as an array
            'paquetes.*' => 'integer|exists:paquetes,id', // Validate each item in the array
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $anuncio = Anuncio::find($id);

        if (!$anuncio) {
            return response()->json(['success' => false, 'message' => 'Anuncio not found'], 404);
        }

        // Update basic fields
        $anuncio->titulo = $request->titulo;
        $anuncio->descripcion = $request->descripcion;

        // Update producto_id if provided
        if ($request->has('producto_id')) {
            $anuncio->producto_id = $request->producto_id;
        }

        $anuncio->save();

        // Sync the paquetes (this will delete old and add new ones)
        if ($request->has('paquetes')) {
            $paqueteIds = $request->paquetes;
            $paquetesData = [];
            $paquetes = Paquete::find($paqueteIds);

            foreach ($paquetes as $paquete) {
                $paquetesData[$paquete->id] = [
                    'tipo' => $paquete->tipopaquete,
                    'costo' => $paquete->costo,
                    'orden' => $paquete->orden,
                    'fechafin' => now()->addDays($paquete->tiempo), // Example logic for expiration
                    'tiempo' => $paquete->tiempo,
                    'estadocompra' => 'activo',
                    'codigopago' => 'N/A', // Or generate a code
                    'label' => $paquete->label,
                    'color' => $paquete->color,
                ];
            }
            $anuncio->paquetes()->sync($paquetesData);
        }

        // Return the updated model with its relations loaded
        $anuncio->load('fotos', 'producto', 'anuncioPaquete.paquete');

        return response()->json(['success' => true, 'data' => $anuncio], 200);
    }

    public function changeState(Request $request, $id)
    {
        $anuncio = Anuncio::find($id);
        if ($request->state) {
            $anuncio->estadoanuncio = 'activo';
        } else {
            $anuncio->estadoanuncio = 'inactivo';
        }
        $anuncio->save();

        return response()->json(['success' => true], 200);
    }
}
