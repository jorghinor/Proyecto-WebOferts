<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Negocio;
use App\Models\Anuncio;
use App\Models\Paquete;
use App\Models\Foto;
use App\Models\AnuncioPaquete;
use App\Models\Producto;
use Faker\Factory as Faker;

class AnunciosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $paquete = Paquete::find(1); // Changed from 3 to 1

        $negocios = Negocio::where('estadonegocio', 'activo')->get();

        $negos = array();

        foreach (range(1, 50) as $index) {
            $idNegocio = rand(1, count($negocios)-1);
            if(!in_array($idNegocio, $negos)){
                $negos[] = $idNegocio;

                $anuncio= new Anuncio();
                $anuncio->titulo = $faker->realText($maxNbChars = 25, $indexSize = 2);
                $anuncio->descripcion = $faker->realText($maxNbChars = 190, $indexSize = 2);
                $anuncio->estadoanuncio = "activo";
                $anuncio->negocio_id = $idNegocio;
                $anuncio->codigo = time();
                    $producto = new Producto();
                    $producto->nombre = $anuncio->titulo;
                    $producto->tipoproducto = 'uno';
                    $producto->estado_prod = 'activo';
                    $producto->negocio_id = $idNegocio;
                    $producto->save();
                $anuncio->producto_id = $producto->id;
                $anuncio->save();

                //$foto = new Foto();
                //$foto->url = '/assets/img/logo.png'; // Use a static local image
                $foto = new Foto();
                if($idNegocio%2==0){
                  $foto->url = $faker->imageUrl(460, 460, 'animals', true);
                } else {
                  $foto->url = $faker->imageUrl(350, 470, 'animals', true);
                }
                $foto->anuncio_id = $anuncio->id;
                $foto->save();

                $anuncio_paquete = new AnuncioPaquete();
                $anuncio_paquete->tipo = $paquete->tipopaquete;
                $anuncio_paquete->costo = $paquete->costo;
                $anuncio_paquete->orden = $paquete->orden;
                $tiempo = rand(1, 6);
                $anuncio_paquete->tiempo = $tiempo;
                $calcularfecha = date('Y-m-d', strtotime('+'.$tiempo.' months'));
                $anuncio_paquete->fechafin = $calcularfecha;
                $anuncio_paquete->estadocompra = 'activo';
                $anuncio_paquete->label = '--';
                $anuncio_paquete->color = '#fff';
                $anuncio_paquete->anuncio_id = $anuncio->id;
                $anuncio_paquete->paquete_id = $paquete->id;
                $anuncio_paquete->save();
            }

        }
    }
}
