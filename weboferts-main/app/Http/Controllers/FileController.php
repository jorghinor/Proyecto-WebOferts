<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Image;
use Intervention\Image\ImageManager;
use App\Models\Foto;

class FileController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function archivo($filename)
    {
        return url('/').Storage::url('public/images/'.$filename);
    }

    public function displayImage($filename)
    {
        $url = Storage::url('images/'.$filename);
        return "<img src= '".$url."'  />";
    }

    public function imageUpload(Request $request){
        // Este método parece ser para el logo del negocio u otros usos
        $validatedData = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,png,jpg,gif|max:5000'
        ]);

        if ($validatedData->fails()) {
            return response()->json(['success' => false, 'message' => $validatedData->errors()], 422);
        }

        if ($file = $request->file('imagen')) {
            $fileName = time() . '.webp';
            $originalPath = storage_path('app/public/images/');
            $thumbnailPath = storage_path('app/public/images/thumbnail/');

            if (!file_exists($originalPath)) mkdir($originalPath, 0777, true);
            if (!file_exists($thumbnailPath)) mkdir($thumbnailPath, 0777, true);

            // Guardar imagen optimizada
            Image::make($file)
                ->resize(800, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 80)
                ->save($originalPath . $fileName);

            // Guardar thumbnail
            Image::make($file)
                ->resize(200, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp', 70)
                ->save($thumbnailPath . $fileName);

            $url = Storage::url('images/' . $fileName);

            return response()->json([
                'success' => true,
                'imageName'=> $url,
                'message' => 'Imagen subida y optimizada.'
            ], 200);
        }
    }

    public function fotoUpload(Request $request){
        // Este método es para las fotos de los anuncios
        $validatedData = Validator::make($request->all(), [
            'imagen' => 'required|mimes:jpeg,png,jpg,gif|max:5000',
            'a_id' => 'required|integer|exists:anuncios,id'
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'success' => false,
                'data' => 'FotoUpload Validation Error.',
                'message' => $validatedData->errors()
            ], 422);
        }

        if ($file = $request->file('imagen')) {
            $input = $request->all();

            // Generar nombre único .webp
            $fileName = time() . uniqid() . '.webp';

            $originalPath = storage_path('app/public/images/');
            $thumbnailPath = storage_path('app/public/images/thumbnail/');

            if (!file_exists($originalPath)) mkdir($originalPath, 0777, true);
            if (!file_exists($thumbnailPath)) mkdir($thumbnailPath, 0777, true);

            // 1. Imagen Principal (Max 1024px, WebP, 75% calidad)
            Image::make($file)
                ->resize(1024, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('webp', 75)
                ->save($originalPath . $fileName);

            // 2. Thumbnail (Max 300px, WebP, 60% calidad)
            Image::make($file)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->encode('webp', 60)
                ->save($thumbnailPath . $fileName);

            $url = Storage::url('images/' . $fileName);

            $foto = Foto::where('anuncio_id', $input['a_id'])
                ->orderBy('id')
                ->first();

            if ($foto) {
                $this->deleteManagedAnuncioImage($foto->url);
            } else {
                $foto = new Foto;
                $foto->anuncio_id = $input['a_id'];
            }

            $foto->url = $url;
            $foto->save();

            return response()->json([
                'success' => true,
                'foto'=> [ "url"=>$url, "id"=>$foto->id ],
                'message' => 'Imagen optimizada correctamente.'
            ], 200);
        }
    }

    private function deleteManagedAnuncioImage($url)
    {
        if (!is_string($url) || $url === '') {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        if (strpos($path, '/storage/images/') !== 0) {
            return;
        }

        $filename = basename($path);
        Storage::delete('public/images/' . $filename);
        Storage::delete('public/images/thumbnail/' . $filename);
    }

    public function deleteFile($filename){
       // Intentar borrar ambas versiones (original y webp si existe)
       Storage::delete('public/images/'.$filename);
       Storage::delete('public/images/thumbnail/'.$filename);

       // También intentar borrar la versión webp si el nombre original no la tenía
       $webpName = pathinfo($filename, PATHINFO_FILENAME) . '.webp';
       Storage::delete('public/images/'.$webpName);
       Storage::delete('public/images/thumbnail/'.$webpName);

       return response()->json(['status' => 'ok'], 200);
    }
}
