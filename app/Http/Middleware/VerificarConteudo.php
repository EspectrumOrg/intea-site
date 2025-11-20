<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ServicoModeracao;
use App\Models\PalavraProibidaGlobal;

class VerificarConteudo
{
    public function handle(Request $request, Closure $next)
    {
        // Apenas para requisições que contenham texto
        if ($request->has('texto_postagem') || $request->has('conteudo')) {
            $texto = $request->texto_postagem ?? $request->conteudo;
            
            // Verificar palavras proibidas globais
            $violacoes = PalavraProibidaGlobal::verificarTexto($texto);
            
            if (!empty($violacoes)) {
                return response()->json([
                    'sucesso' => false,
                    'mensagem' => 'Seu conteúdo contém palavras proibidas. Por favor, revise sua mensagem.',
                    'tipo' => 'conteudo_proibido',
                    'violacoes' => count($violacoes)
                ], 422);
            }
        }

        return $next($request);
    }
}