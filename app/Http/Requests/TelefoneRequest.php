<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelefoneRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'numero_telefone' => 'required|string|max:20',
            'tipo_telefone' => 'required|in:celular,residencial,comercial,whatsapp',
            'is_principal' => 'sometimes|boolean'
        ];
    }

    public function messages()
    {
        return [
            'numero_telefone.required' => 'O número de telefone é obrigatório.',
            'numero_telefone.max' => 'O número de telefone não pode ter mais de 20 caracteres.',
            'tipo_telefone.required' => 'O tipo de telefone é obrigatório.',
            'tipo_telefone.in' => 'Tipo de telefone inválido.'
        ];
    }
}