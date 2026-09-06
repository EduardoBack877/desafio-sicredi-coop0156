<?php

namespace App\Http\Requests;

use App\Enums\TipoCredito;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SolicitarAnaliseCreditoRequest extends FormRequest
{
    /**
     * Autoriza a solicitação da análise.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação da solicitação de crédito.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => [
                'required',
                'string',
                'max:255',
            ],

            'cpf' => [
                'required',
                'digits:11',
            ],

            'renda_mensal' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'tipo_credito' => [
                'required',
                Rule::enum(TipoCredito::class),
            ],

            'valor_solicitado' => [
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

            'renda_mensal.required' => 'A renda mensal é obrigatória.',
            'renda_mensal.numeric' => 'A renda mensal deve ser numérica.',
            'renda_mensal.gt' => 'A renda mensal deve ser maior que zero.',

            'tipo_credito.required' => 'O tipo de crédito é obrigatório.',
            'tipo_credito.enum' => 'O tipo de crédito informado é inválido.',

            'valor_solicitado.required' => 'O valor solicitado é obrigatório.',
            'valor_solicitado.numeric' => 'O valor solicitado deve ser numérico.',
            'valor_solicitado.gt' => 'O valor solicitado deve ser maior que zero.',
        ];
    }
}