<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnuncioPaquete extends Model
{
    use HasFactory;
    protected $table = "anuncio_paquete";
    protected $primarykey = 'id';
    protected $fillable = [
        'tipo',
        'costo',
        'orden',
        'fechafin',
        'tiempo',
        'estadocompra',
        'codigopago',
        'label',
        'color',
        'anuncio_id',
        'paquete_id',
    ];

    /**
     * Get the paquete associated with the AnuncioPaquete.
     */
    public function paquete()
    {
        return $this->belongsTo(Paquete::class);
    }
}
