<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Negocio extends Model
{
    use HasFactory;
    protected $table = 'negocios';
    protected $primarykey = 'id';
    protected $fillable = [
        'nnegocio',
        'ciudad',
        'nit',
        'dir',
        'gmaps',
        'latitude',
        'longitud',
        'telfs',
        'celular',
        'delivery',
        'web',
        'logo',
        'estadonegocio',
        'compra',
        'user_id'
    ];
    //atributo oculto
    protected $hidden = ['user_id'];

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function anuncios()
    {
        return $this->hasMany(Anuncio::class);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
