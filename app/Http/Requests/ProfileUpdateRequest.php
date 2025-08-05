<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(Usuario::class)->ignore($this->user()->id)],
            'user' => 'nullable|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'cpf' => 'nullable|string|size:11',
            'genero' => 'nullable|string|max:255',
            'data_nascimento' => 'nullable|date',
            'logradouro' => 'nullable|string|max:255',
            'rua' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'complemento' => 'nullable|string|max:255',
        ];
    }
}
