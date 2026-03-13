<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;
    protected $table = "paquetes";
    protected $primarykey = 'id';
    protected $fillable = [
        'titulo',
        'descripcion',
        'costo',
        'tiempo',
        'estado',
        'orden',
        'label',
        'color',
        'tipopaquete'
    ];

    public function anuncios()
    {
        return $this->belongsToMany(Anuncio::class);
    }
}