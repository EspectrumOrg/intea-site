<?php

namespace App\Http\Requests;

use App\Models\User;
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
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'apelido' => ['nullable', 'string', 'max:100'],
            'descricao' => ['nullable', 'string', 'max:500'],
            'data_nascimento' => ['required', 'date'],
            'genero_id' => ['required', 'exists:generos,id'],
            'foto' => ['nullable', 'image', 'max:2048'],
            // Remoção de 'cpf' e 'logradouro' das regras de validação
        ];
    }
}