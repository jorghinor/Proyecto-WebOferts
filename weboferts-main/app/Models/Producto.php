<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;
    protected $table = 'productos';
    protected $primarykey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_regular',
        'precio_oferta',
        'stock', // Nuevo campo
        'estado_prod',
        'tipoproducto',
        'imagen',
        'negocio_id'
    ];

    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class);
    }
}
