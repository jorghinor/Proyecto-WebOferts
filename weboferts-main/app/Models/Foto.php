<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Anuncio;

class Foto extends Model
{
    use HasFactory;
    protected $table = 'fotos';
    protected $primarykey = 'id';
    protected $fillable = array(
        'url',
        'anuncio_id',
    );

    public function anuncio()
    {
        return $this->belongsTo(Anuncio::class);
    }
}
