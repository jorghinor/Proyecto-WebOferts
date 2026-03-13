<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaquete extends Model
{
    use HasFactory;
    //agregado
    protected $table = 'user_paquete';
    protected $primarykey = 'id';
    protected $fillable = [
        'precio',
        'fecha_fin',
        'estado',
        'codigo',
        'user_id',
        'paquete_id',
        'orden'
    ];
    //protected $hidden = ['user_id'];
}