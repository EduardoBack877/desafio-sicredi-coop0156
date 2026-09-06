<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    /**
     * Define se a requisição está autorizada.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para atualização de cliente.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $clienteId = $this->route('cliente');

        return [
            'nome' => ['sometimes', 'required', 'string', 'max:255'],

            'cpf' => [
                'sometimes',
                'required',
                'digits:11',
                Rule::unique('clientes', 'cpf')->ignore($clienteId),
            ],

            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('clientes', 'email')->ignore($clienteId),
            ],

            'telefone' => ['sometimes', 'nullable', 'string', 'max:30'],

            'renda_mensal' => [
                'sometimes',
                'required',
                'numeric',
                'gt:0',
            ],
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
