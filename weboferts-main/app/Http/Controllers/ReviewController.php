<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Negocio;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function index($negocioId)
    {
        $reviews = Review::where('negocio_id', $negocioId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($review) {
                // Si hay usuario registrado, usamos su nombre, si no, el nombre del invitado
                $name = $review->user ? $review->user->name : $review->reviewer_name;
                return [
                    'id' => $review->id,
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'user_name' => $name // Enviamos un campo unificado
                ];
            });

        $average = $reviews->avg('rating');

        return response()->json([
            'reviews' => $reviews,
            'average' => round($average, 1),
            'count' => $reviews->count()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'negocio_id' => 'required|exists:negocios,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'reviewer_name' => 'required|string|max:50' // Nombre obligatorio para invitados
        ]);

        // Crear nueva reseña como invitado
        Review::create([
            'user_id' => null,
            'reviewer_name' => $request->reviewer_name,
            'negocio_id' => $request->negocio_id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Gracias por tu calificación.'], 200);
    }
}
