<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Negocio;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categorias = [];
        foreach (Categoria::all() as $cat) {
            array_push($categorias, $cat->id);
        }

        foreach (Negocio::all() as $nego) {
            $id1 = rand(0, count($categorias)-1);
            //$cate1 = Categoria::find($id1);
            $id2 = rand(1, count($categorias)-1);
            //$cate2 = Categoria::find($id2);
            $negocio  = Negocio::find($nego->id);
            if($id1>0 and $id2>0){
                if($id1!=$id2){
                    $negocio->categorias()->sync([ $categorias[$id1], $categorias[$id2]]);
                }else {
                    $negocio->categorias()->sync([ $categorias[$id1]]);
                }
            }
            //$negocio->save();
        }
    }
}
