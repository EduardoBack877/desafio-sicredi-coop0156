<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    /**
     * Define se a requisição está autorizada.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para criação de cliente.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'digits:11', 'unique:clientes,cpf'],
            'email' => ['required', 'email', 'unique:clientes,email'],
            'telefone' => ['nullable', 'string', 'max:30'],
            'renda_mensal' => ['required', 'numeric', 'gt:0'],
        ];
    }

    /**
     * Mensagens personalizadas de validação.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório.',

            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.digits' => 'O CPF deve conter exatamente 11 dígitos.',
            'cpf.unique' => 'Este CPF já está cadastrado.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado.',

            'renda_mensal.required' => 'A renda mensal é obrigatória.',
            'renda_mensal.numeric' => 'A renda mensal deve ser numérica.',
            'renda_mensal.gt' => 'A renda mensal deve ser maior que zero.',
        ];
    }
}
