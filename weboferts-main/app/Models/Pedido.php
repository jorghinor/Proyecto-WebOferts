<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'user_id',
        'nombre_cliente',
        'email_cliente',
        'telefono_cliente',
        'total',
        'estado',
        'metodo_pago'
    ];

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
