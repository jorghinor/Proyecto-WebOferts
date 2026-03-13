<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Foto;
use App\Models\Paquete;
use App\Models\Producto; // Make sure Producto is imported

class Anuncio extends Model
{
    use HasFactory;
    protected $table = 'anuncios';
    protected $primarykey = 'id';
    protected $fillable = array(
        'titulo',
        'descripcion',
        'estadoanuncio',
        'codigo',
        'negocio_id',
        'producto_id'
    );

    protected $hidden = ['updated_at'];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }

    public function paquetes()
    {
        return $this->belongsToMany(Paquete::class);
    }

    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }

    // --- START OF FIX ---
    // An Anuncio BELONGS TO a Producto.
    // The foreign key 'producto_id' is on the 'anuncios' table.
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
    // --- END OF FIX ---

    // This is the correct way to define the relationship with AnuncioPaquete
    public function anuncioPaquete()
    {
        return $this->hasOne(AnuncioPaquete::class);
    }
}
