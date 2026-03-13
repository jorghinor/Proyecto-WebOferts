<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $table = 'categorias';
    protected $primarykey = 'id';
    protected $fillable = [
        'cname',
        'icon',
        'cstate',
        'url'
    ];

    public function negocios()
    {
        return $this->belongsToMany(Negocio::class);
    }
}
