<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Usuario;

Route::get('/usuarios/buscar', function (Request $request) {
    $query = $request->get('q');
    $excludeCurrent = $request->get('exclude_current', false);
    
    if (!$query || strlen($query) < 2) {
        return response()->json([]);
    }
    
    $usuarios = Usuario::where(function($q) use ($query) {
            $q->where('nome', 'LIKE', "%{$query}%")
              ->orWhere('apelido', 'LIKE', "%{$query}%")
              ->orWhere('user', 'LIKE', "%{$query}%")
              ->orWhere('email', 'LIKE', "%{$query}%");
        })
        ->where('ativo', true)
        ->limit(10)
        ->get(['id', 'nome', 'apelido', 'user', 'email', 'foto']);
    
    // Excluir usuário atual se solicitado
    if ($excludeCurrent && auth()->check()) {
        $usuarios = $usuarios->filter(function($usuario) {
            return $usuario->id !== auth()->id();
        })->values();
    }
    
    return response()->json($usuarios);
});