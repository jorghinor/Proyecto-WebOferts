<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewer_name', // Nuevo campo
        'negocio_id',
        'rating',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function negocio()
    {
        return $this->belongsTo(Negocio::class);
    }
}
