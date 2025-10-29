<?php

namespace App\Http\Controllers;

use App\Models\FoneUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TelefoneController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Log::info('Tentando criar telefone', $request->all());
            
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            // Validação
            $validated = $request->validate([
                'numero_telefone' => 'required|string|max:20',
                'tipo_telefone' => ['required', Rule::in(['celular', 'residencial', 'comercial', 'whatsapp'])],
                'is_principal' => 'sometimes|boolean'
            ]);

            // Se marcar como principal, remove principal dos outros
            if ($request->boolean('is_principal')) {
                FoneUsuario::where('usuario_id', $user->id)
                    ->update(['is_principal' => false]);
            } else {
                // Se não tem telefones, o primeiro será principal
                $countTelefones = FoneUsuario::where('usuario_id', $user->id)->count();
                if ($countTelefones === 0) {
                    $validated['is_principal'] = true;
                }
            }

            $telefone = FoneUsuario::create([
                'usuario_id' => $user->id,
                'numero_telefone' => $validated['numero_telefone'],
                'tipo_telefone' => $validated['tipo_telefone'],
                'is_principal' => $validated['is_principal'] ?? false
            ]);

            Log::info('Telefone criado com sucesso', ['telefone_id' => $telefone->id]);

            return response()->json([
                'success' => true,
                'message' => 'Telefone adicionado com sucesso!',
                'telefone' => $telefone
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao criar telefone', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao criar telefone: ' . $e->getMessage(), [
                'request' => $request->all(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao adicionar telefone. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            Log::info('Tentando atualizar telefone', ['telefone_id' => $id, 'data' => $request->all()]);
            
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            // Validação
            $validated = $request->validate([
                'numero_telefone' => 'required|string|max:20',
                'tipo_telefone' => ['required', Rule::in(['celular', 'residencial', 'comercial', 'whatsapp'])],
                'is_principal' => 'sometimes|boolean'
            ]);

            $telefone = FoneUsuario::where('id', $id)
                ->where('usuario_id', $user->id)
                ->firstOrFail();

            // Se marcar como principal, remove principal dos outros
            if ($request->boolean('is_principal')) {
                FoneUsuario::where('usuario_id', $user->id)
                    ->where('id', '!=', $id)
                    ->update(['is_principal' => false]);
            }

            $telefone->update([
                'numero_telefone' => $validated['numero_telefone'],
                'tipo_telefone' => $validated['tipo_telefone'],
                'is_principal' => $validated['is_principal'] ?? $telefone->is_principal
            ]);

            Log::info('Telefone atualizado com sucesso', ['telefone_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Telefone atualizado com sucesso!',
                'telefone' => $telefone
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Telefone não encontrado para atualização', ['telefone_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Telefone não encontrado.'
            ], 404);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação ao atualizar telefone', ['errors' => $e->errors()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar telefone: ' . $e->getMessage(), [
                'telefone_id' => $id,
                'request' => $request->all(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar telefone. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            Log::info('Tentando excluir telefone', ['telefone_id' => $id]);
            
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }

            $telefone = FoneUsuario::where('id', $id)
                ->where('usuario_id', $user->id)
                ->firstOrFail();

            $wasPrincipal = $telefone->is_principal;
            
            $telefone->delete();

            // Se excluiu o principal, definir um novo principal (o primeiro)
            if ($wasPrincipal) {
                $novoPrincipal = FoneUsuario::where('usuario_id', $user->id)
                    ->orderBy('id')
                    ->first();
                    
                if ($novoPrincipal) {
                    $novoPrincipal->update(['is_principal' => true]);
                }
            }

            Log::info('Telefone excluído com sucesso', ['telefone_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Telefone removido com sucesso!'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Telefone não encontrado para exclusão', ['telefone_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Telefone não encontrado.'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erro ao excluir telefone: ' . $e->getMessage(), [
                'telefone_id' => $id,
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover telefone. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Set as principal phone
     */
    public function setPrincipal($id)
    {
        try {
            Log::info('Definindo telefone como principal', ['telefone_id' => $id]);
            
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado.'
                ], 401);
            }
            
            // Remove principal de todos
            FoneUsuario::where('usuario_id', $user->id)
                ->update(['is_principal' => false]);
            
            // Define o selecionado como principal
            $telefone = FoneUsuario::where('id', $id)
                ->where('usuario_id', $user->id)
                ->firstOrFail();
                
            $telefone->update(['is_principal' => true]);

            Log::info('Telefone definido como principal com sucesso', ['telefone_id' => $id]);

            return response()->json([
                'success' => true,
                'message' => 'Telefone definido como principal!',
                'telefone' => $telefone
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Telefone não encontrado', ['telefone_id' => $id]);
            
            return response()->json([
                'success' => false,
                'message' => 'Telefone não encontrado.'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Erro ao definir telefone principal: ' . $e->getMessage(), [
                'telefone_id' => $id,
                'user_id' => auth()->id(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao definir telefone principal. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Get phone data for editing
     */
    public function getDados($id)
    {
        try {
            $user = auth()->user();
            $telefone = FoneUsuario::where('id', $id)
                ->where('usuario_id', $user->id)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'telefone' => $telefone
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Telefone não encontrado.'
            ], 404);
        }
    }
}