<?php

namespace App\Services;

use App\Enums\StatusAnalise;
use App\Models\AnaliseCredito;
use App\Models\Cliente;

class AnaliseCreditoService
{
    private const RENDA_MINIMA = 1500.00;
    private const SCORE_MINIMO = 400;
    private const SCORE_ALTO = 700;

    private const TAXA_SCORE_MEDIO = 4.5;
    private const TAXA_SCORE_ALTO = 2.9;

    private const QUANTIDADE_PARCELAS = 12;
    private const LIMITE_COMPROMETIMENTO_RENDA = 0.30;

    public function __construct(
        private readonly BureauService $bureauService
    ) {
    }

    /**
     * Executa o fluxo completo de uma análise de crédito.
     */
    public function solicitar(array $dados): AnaliseCredito
    {
        /*
         * 1. Localiza ou cria automaticamente o cliente pelo CPF.
         */
        $cliente = Cliente::firstOrCreate(
            [
                'cpf' => $dados['cpf'],
            ],
            [
                'nome' => $dados['nome'],
                'renda_mensal' => $dados['renda_mensal'],
            ]
        );

        /*
         * 2. Persiste a análise inicialmente como pendente.
         */
        $analise = AnaliseCredito::create([
            'cliente_id' => $cliente->id,
            'cpf' => $dados['cpf'],
            'nome' => $dados['nome'],
            'renda_mensal' => $dados['renda_mensal'],
            'tipo_credito' => $dados['tipo_credito'],
            'valor_solicitado' => $dados['valor_solicitado'],
            'status' => StatusAnalise::PENDENTE,
        ]);

        /*
         * 3. Consulta o Bureau.
         *
         * Caso haja timeout, HTTP 500 ou resposta inválida,
         * o BureauService lançará uma exceção.
         * A análise permanece pendente, pois uma indisponibilidade
         * técnica não significa que o cliente foi reprovado.
         */
        $score = $this->bureauService->consultarScore($dados['cpf']);

        /*
         * 4. Regra de renda mínima.
         */
        if ((float) $dados['renda_mensal'] < self::RENDA_MINIMA) {
            return $this->reprovar(
                analise: $analise,
                score: $score,
                motivo: 'Renda mínima insuficiente'
            );
        }

        /*
         * 5. Regra de score mínimo.
         */
        if ($score < self::SCORE_MINIMO) {
            return $this->reprovar(
                analise: $analise,
                score: $score,
                motivo: 'Score de crédito muito baixo'
            );
        }

        /*
         * 6. Define a taxa conforme a faixa do score.
         */
        $taxaJuros = $score >= self::SCORE_ALTO
            ? self::TAXA_SCORE_ALTO
            : self::TAXA_SCORE_MEDIO;

        /*
         * 7. Calcula a parcela com juros simples.
         */
        $valorParcela = $this->calcularParcela(
            (float) $dados['valor_solicitado'],
            $taxaJuros
        );

        /*
         * 8. Verifica comprometimento máximo de 30% da renda.
         */
        $limiteParcela =
            (float) $dados['renda_mensal']
            * self::LIMITE_COMPROMETIMENTO_RENDA;

        if ($valorParcela > $limiteParcela) {
            return $this->reprovar(
                analise: $analise,
                score: $score,
                motivo: 'Comprometimento de renda superior a 30%',
                taxaJuros: $taxaJuros,
                valorParcela: $valorParcela
            );
        }

        /*
         * 9. Crédito aprovado.
         */
        $analise->update([
            'status' => StatusAnalise::APROVADO,
            'score' => $score,
            'taxa_juros' => $taxaJuros,
            'valor_parcela' => $valorParcela,
            'motivo_rejeicao' => null,
        ]);

        return $analise->fresh();
    }

    /**
     * Calcula o valor das 12 parcelas utilizando juros simples.
     */
    private function calcularParcela(
        float $valorSolicitado,
        float $taxaJuros
    ): float {
        $jurosTotais =
            $valorSolicitado
            * ($taxaJuros / 100)
            * self::QUANTIDADE_PARCELAS;

        $valorTotal = $valorSolicitado + $jurosTotais;

        return round(
            $valorTotal / self::QUANTIDADE_PARCELAS,
            2
        );
    }

    /**
     * Centraliza a atualização de uma análise reprovada.
     */
    private function reprovar(
        AnaliseCredito $analise,
        int $score,
        string $motivo,
        ?float $taxaJuros = null,
        ?float $valorParcela = null
    ): AnaliseCredito {
        $analise->update([
            'status' => StatusAnalise::REPROVADO,
            'score' => $score,
            'taxa_juros' => $taxaJuros,
            'valor_parcela' => $valorParcela,
            'motivo_rejeicao' => $motivo,
        ]);

        return $analise->fresh();
    }
    /**
 * Confirma a contratação de uma análise aprovada.
 */
public function contratar(int $id): AnaliseCredito
{
    $analise = AnaliseCredito::findOrFail($id);

    if ($analise->status !== StatusAnalise::APROVADO) {
        throw new \DomainException(
            'A análise de crédito não está aprovada para contratação.'
        );
    }

    $analise->update([
        'status' => StatusAnalise::CONTRATADO,
    ]);

    return $analise->fresh();
}
}